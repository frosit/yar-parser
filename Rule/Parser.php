<?php
/**
 *     Copyright (c) 2016.  GDPRProof B.V.
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU Affero General Public License as
 *     published by the Free Software Foundation, either version 3 of the
 *     License, or (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU Affero General Public License for more details.
 *
 *     You should have received a copy of the GNU Affero General Public License
 *     along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *     @package     Yar Parser
 *     @author      Fabio Ros <f.ros@gdprproof.com> - <fabio@frosit.nl>
 *     @copyright   Copyright (c) 2016 Fabio Ros - GDPRProof B.V.
 *     @license     https://opensource.org/licenses/AGPL-3.0 GNU Affero General Public License version 3
 *
 */
/**
 * @todo add string cleaner
 */

namespace Frosit\Component\Yar\Rule;

use Frosit\Component\Yar\Exception\ParseException;
use Frosit\Component\Yar\Regexer;
#use Frosit\Component\Yar\Rule\Rule;
use Frosit\Component\Yar\Unescaper;

/**
 * Class RuleParser
 * @package Frosit\Yarparser\Rule
 */
class Parser
{

    /**
     * @var \Frosit\Component\Yar\Rule\Rule
     */
    public $rule;

    /**
     * @var bool
     */
    public $doRegex = true;
    /**
     * @var bool
     */
    public $walkLines = false;
    /**
     * @var bool
     */
    public $saveAsPreg = false;

    // placeholders
    /**
     * @var
     */
    public $content;
    /**
     * @var
     */
    public $head;
    /**
     * @var
     */
    public $body;


    /**
     * Settings for parsing per line
     */
    // settings
    /**
     * @var int
     */
    private $offset = 0;
    /**
     * @var int
     */
    private $indentation = 4; // default indentation, = 1 tab @todo
    /**
     * @var array
     */
    private $blockNames = array("meta", "strings", "condition");

    // stats
    /**
     * @var
     */
    private $totalNumberOfLines;
    /**
     * @var array
     */
    public $lines = array();

    // runtime variables
    /**
     * @var int
     */
    private $currentLineNb = -1;
    /**
     * @var string
     */
    private $currentLine = '';

    /**
     * @var array
     */
    private $skippedLineNumbers = array();
    /**
     * @var array
     */
    private $locallySkippedLineNumbers = array();
    /**
     * @var bool
     */
    private $inBlockComment = false;

    /**
     * @var array
     */
    public $sections = array();

    /**
     * @var bool
     */
    public $debug = false;

    /**
     * Parser constructor.
     */
    public function __construct()
    {
        $this->rule = new Rule();
    }

    /**
     * Parse function
     *
     * This function practically runs the whole parse process per rule
     * @return array|\Frosit\Component\Yar\Rule\Rule
     * @internal param $content
     */
    public function parse($ruleContent, $regex = true)
    {

        $this->content = $ruleContent;
        $this->head = $ruleContent['head'][0];
        $this->body = $ruleContent['body'][0];

        $this->parseHead();

        /**
         * @todo add optional cleaning or detect it
         */
//        $body = $this->body = Regexer::removeBlockCommentIndentSafe($this->body, $this->indentation);
//        $body = $this->body = $this->tokenize($this->body);

        $lines = $this->lines = array_filter(explode(PHP_EOL, $this->body));

        /**
         * @todo parse by regex or walking lines? or use both to validate
         */
        if ($this->doRegex) {
            $this->parseStringsByRegex();
            $this->parseConditionsByRegex();
            $this->parseMetaByRegex();
        }
        if ($this->walkLines) {
            while ($this->moveToNextLine()) {

                // if current line is a start or end of block comment, skip it and set the parameter $this->inBlockComment
                if ($this->isCurrentLineBlockComment()) {
                    continue;
                }

                // if a line is empty, blank or a oneline comment, skip it
                if ($this->isCurrentLineEmpty() || $this->isCurrentLineBlank() || $this->isCurrentLineComment() || $this->isCurrentLineBracket()
                ) {
                    continue;
                }

                // if the line is in skipped, skip it because it was already processed
                if (in_array($this->currentLineNb, $this->skippedLineNumbers)) {
                    continue;
                }

                // If the next line is indented, this line is the start of a section (meta, strings, etc)
                if ($this->isNextLineIndented()) {
                    // @todo do code block
                    if ($this->processIndentedBlock()) { // @todo hotfix with tabs
                        $this->moveToPreviousLine(); // @todo temp test
                    } else {
                        $this->error('Problems with processing indented block');
                    }

                } else { // else it is a inline section
                    $this->parseInline();
                }

                // if we're at the last line, do something
                if ($this->isCurrentLineLastLineInDocument()) {

                }
            }
            foreach ($this->sections as $key => $section) {
                if ($key === 'strings') {
                    if (is_array($section)) {
                        // @todo add array
                    } else {
                        $this->rule->setString(
                            array("value" => $section, "type" => $this->detectStringType($section))
                        ); // @todo change spec
                    }
                } elseif ($key === 'conditions') {
                    if (isset($section)) {
                        $this->rule->setCondition($section); // @todo change spec
                    }
                }
            }
        }

        // return sections
        return $this->rule;
    }

    /**
     * Parse the first line (head)
     * @note delivers name and tags
     * @return $this
     */
    public function parseHead()
    {
        $head = explode(":", $this->head);
        $this->rule->setName(trim($head[0]));
        if (isset($head[1])) {
            $tags = array_filter(explode(" ", $head[1]));
            $this->rule->setTags($tags);
        }

        return $this;
    }

    /**
     * Parse strings by regex
     * @return bool
     */
    public function parseStringsByRegex()
    {
        if ($strings = Regexer::matchStringsEncapsulatedNonSpecific($this->body)) {
            foreach ($strings as $string) {
                $stringVal = $string['value'];
                $type = $this->detectStringType($stringVal);
                $unescaper = new Unescaper();
                if ($type === "string") {
                    $stringVal = $unescaper->unescapeDoubleQuotedString(trim($stringVal, "\""));
                    if ($this->saveAsPreg) {
                        $stringVal = preg_quote($strings['value'], '/');
                    }
                }
                if ($type === "regex") {
                    $stringVal = trim(($stringVal), "/"); // @todo make sure this only happens once
                }

                $newString = array(
                    'value' => $stringVal,
                    'name' => isset($string['name']) ? $string['name'] : null,
                    'type' => $type ?: false,
                    'tags' => isset($string['tags']) ? $string['tags'] : null,
                );
                $this->rule->setString($newString);
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * @param $string
     * @return bool|string
     */
    public function detectStringType($string)
    {
        $string = trim($string);
        if (substr($string, 0, 1) === "\"") {
            return "string";
        }
        if (substr($string, 0, 1) === "/") {
            return "regex";
        }
        if (substr($string, 0, 1) === "{") {
            return "hex";
        }

        return false;
    }

    /**
     * Parse strings by regex
     * @return bool
     */
    public function parseConditionsByRegex()
    {
        if ($conditions = Regexer::matchConditions($this->body)) {
            // @todo process conditions
            foreach ($conditions as $condition) {
                $this->rule->setCondition($condition['conditions'][0]);
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * Parses meta section iwth regex
     */
    public function parseMetaByRegex()
    {
        if ($meta = Regexer::matchMeta($this->body)) {

            $preppedMeta = array();
            foreach ($meta as $item) {
                $value = trim($item['value']);
                if (substr($value, 0, 1) === "\"" && substr($value, -1, 1) === "\"") {
                    $value = trim($value, "\"");
                }
                $preppedMeta[$item['name']] = $value;
            }
            if($preppedMeta){
                $this->rule->setMeta($preppedMeta);
            }
        }
    }

    /**
     * @param $source
     * @return string
     */
    public function tokenize($source)
    {
        if (!defined('T_ML_COMMENT')) {
            define('T_ML_COMMENT', T_COMMENT);
        } else {
            define('T_DOC_COMMENT', T_ML_COMMENT);
        }

//        $source = file_get_contents('example.php');
        $tokens = token_get_all($source);

        $newSource = '';
        foreach ($tokens as $token) {
            if (is_string($token)) {
                // simple 1-character token
                echo $token;
            } else {
                // token array
                list($id, $text) = $token;

                switch ($id) {
                    case T_COMMENT:
                    case T_ML_COMMENT: // we've defined this
                    case T_DOC_COMMENT: // and this
                        // no action on comments
                        break;

                    default:
                        // anything else -> output "as is"
                        $newSource .= $text;
                        break;
                }
            }
        }

        return $newSource;
    }

    /**
     * Processes an indented block / section like meta / strings
     * @todo modify to new spec
     */
    public function processIndentedBlock()
    {
        $section = array();
        $blockName = $this->getIndentedBlockName();

        if ($blockName) {

            $blockLines = $this->getBlockLines(); // the indented block lines of this section

            if ($blockName === "meta") {
                foreach ($blockLines as $blockLine) {
                    $split = explode("=", $blockLine, 2);
                    $lineName = $this->cleanValue($split[0]);
                    $lineValue = $this->cleanValue($split[1]);
                    $section[$lineName] = $lineValue;
                }
            } elseif ($blockName === "strings") {
                $stringsHaveName = false;
                foreach ($blockLines as $blockLine) {
                    $split = explode("=", $blockLine, 2);
                    $lineName = $this->cleanValue($split[0]);
                    $lineValue = $this->cleanValue($split[1]);
                    if ($lineName !== "$" || $stringsHaveName) {
                        $stringsHaveName = true;
                        $lineName = str_replace("$", "", $lineName);
                        $section[$lineName] = $lineValue;
                    } else {
                        $section[] = $lineValue;
                    }
                }
            } elseif ($blockName === "condition") {
                $blockName = "conditions";
                foreach ($blockLines as $blockLine) {
                    $lineValue = $this->cleanValue($blockLine);
                    $section[] = $lineValue;
                }
            } else {
                foreach ($blockLines as $blockLine) {
                    $split = explode("=", $blockLine, 2);
                    $section[] = $this->cleanValue($split[1]);
                }
            }

            $this->sections[$blockName] = $section;

            return true;
        } else {
            return false;
        }

    }

    /**
     * Cleans a string / value
     * @param $value
     * @return string
     */
    public function cleanValue($value)
    {
        $value = trim($value); // @todo remove other trims

        $l = substr($value, 0, 1);
        $r = substr($value, -1, 1);
        if ($l === "\"" && $r === "\"") {
            $value = trim($value, "\"");
        }

        return $value;
    }

    /**
     * @return bool
     */
    public function parseInline()
    {
        $name = $this->getIndentedBlockName();

        if (!$name) {
            return false;
        }

        // parse inline strings
        if ($name === "strings") {

            $value = trim(explode("=", $this->currentLine, 2)[1]);
            // @todo string name ($name)

            // parse condition
        } elseif ($name === "condition") {

            $name = "conditions"; // @todo hotfix
            $value = trim(explode("condition:", $this->currentLine, 2)[1]);


        } else { // throw error
            $this->error('Could not parse inline value of rule :: ');
        }

        // clean value
        if (isset($value)) {

            $value = $this->cleanValue($value);

            $this->sections[$name] = $value;

            return true;
        } else {
            return false;
        }
    }


    /**
     * Takes apart the lines with same indentation as the block
     * @return array
     */
    public function getBlockLines()
    {
        $indentation = $this->getCurrentLineIndentation();
        $startLine = $this->currentLineNb;
        $lines = array();
        while ($this->moveToNextLine()) {
            $cur = $this->getCurrentLineIndentation();
            if ($this->getCurrentLineIndentation() !== $indentation) {

                $this->skippedLineNumbers[] = $this->currentLineNb;

                if ($this->isCurrentLineBlank() || $this->isCurrentLineEmpty() || $this->isCurrentLineComment()) {
                    continue;
                }

                $lines[] = $this->currentLine;
            } else {
                break;
            }
        }

        return $lines;
    }

    /**
     * Gets the name of the indented block
     * @todo works for normal as well
     * @return bool|string
     */
    public function getIndentedBlockName()
    {
        $line = $this->currentLine;
        $name = ltrim(explode(":", $line)[0], " ");

        if (!in_array($name, $this->blockNames)) {
            $this->error('Error parsing block name for rule :: ');

            return false;
        }

        return $name;
    }

    /**
     * Error wrapper
     * @param $message
     * @param bool $exception
     * @throws \Frosit\Component\Yar\Exception\ParseException
     */
    public function error($message, $exception = false)
    {

        if ($exception) {
            throw new ParseException($message, $this->currentLine);
        }
    }

    /**
     * Splits lines by preg split
     *
     * @param $ruleContent
     * @return array
     */
    public function splitLines($ruleContent)
    {
        $splitLines = preg_split(
            "/\n/",
            $ruleContent,
            -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE
        );

        return $splitLines;
    }

    /**
     * Returns the current line indentation.
     *
     * @todo test tab / space calcs
     *
     * @return int The current line indentation
     */
    private function getCurrentLineIndentation()
    {
        $length = strlen($this->currentLine) - strlen(ltrim($this->currentLine, ' '));

        $length += $this->indentation * (strlen($this->currentLine) - strlen(ltrim($this->currentLine, "\t")));

        return $length;
    }

    /**
     * Moves the parser to the next line.
     *
     * @return bool
     */
    private function moveToNextLine()
    {
        if ($this->currentLineNb >= count($this->lines) - 1) {
            return false;
        }

        $this->currentLine = $this->lines[++$this->currentLineNb];

        return true;
    }

    /**
     * Moves the parser to the previous line.
     *
     * @return bool
     */
    private function moveToPreviousLine()
    {
        if ($this->currentLineNb < 1) {
            return false;
        }

        $this->currentLine = $this->lines[--$this->currentLineNb];

        return true;
    }

    /**
     * Returns true if the next line is indented.
     *
     * @return bool Returns true if the next line is indented, false otherwise
     */
    private function isNextLineIndented()
    {
        $currentIndentation = $this->getCurrentLineIndentation();
        $EOF = !$this->moveToNextLine();

        while (!$EOF && $this->isCurrentLineEmpty()) {
            $EOF = !$this->moveToNextLine();
        }

        if ($EOF) {
            return false;
        }

        $ret = false;
        if ($this->getCurrentLineIndentation() > $currentIndentation) {
            $ret = true;
        }

        $this->moveToPreviousLine();

        return $ret;
    }

    /**
     * Returns true if the current line is blank or if it is a comment line.
     *
     * @return bool Returns true if the current line is empty or if it is a comment line, false otherwise
     */
    private function isCurrentLineEmpty()
    {
        return $this->isCurrentLineBlank() || $this->isCurrentLineComment();
    }

    /**
     * Returns true if the current line is blank.
     *
     * @return bool Returns true if the current line is blank, false otherwise
     */
    private function isCurrentLineBlank()
    {
        return '' == trim($this->currentLine, ' ');
    }

    /**
     * Checks for the start or an end of a block comment
     *
     * @return bool
     */
    public function isCurrentLineBlockComment()
    {
        //checking explicitly the first char of the trim is faster than loops or strpos
        $ltrimmedLine = ltrim($this->currentLine);

        if (substr($ltrimmedLine, 0, 2) === '*/') { // end block comment
            $this->inBlockComment = false;

            return true;
        }

        if ($this->inBlockComment) {
            return true;
        }

        if (substr($ltrimmedLine, 0, 2) === '/*') { // start block comment
            $this->inBlockComment = true;

            return true;
        }


        return false;
    }

    /**
     * Returns true if the current line is a comment line.
     *
     * @todo better comment processing
     *
     * @return bool Returns true if the current line is a comment line, false otherwise
     */
    private function isCurrentLineComment()
    {
        //checking explicitly the first char of the trim is faster than loops or strpos
        $ltrimmedLine = ltrim($this->currentLine, ' ');

        if (substr($ltrimmedLine, 0, 1) === '#') {
            return true;
        }
        if (substr($ltrimmedLine, 0, 2) === '//') {
            return true;
        }

        $btrimmedLine = trim($this->currentLine);

        if (substr($btrimmedLine, 0, 2) === "/*" && substr($btrimmedLine, -1, 2) === "*/") {
            return true;
        }

        // @todo add this type of comment /* something */

        return '' !== $ltrimmedLine && $ltrimmedLine[0] === '//';
    }

    /**
     * Checks if current line is opening or closing bracket
     * @return bool
     */
    private function isCurrentLineBracket()
    {
        $line = ltrim($this->currentLine);
        if (substr($line, 0, 1) === "{") {
            return true;
        }
        if (substr($line, 0, 1) === "}") {
            return true;
        }

        return false;
    }

    /**
     * Checks if the current line is the last line of the rule
     * @todo closing bracket check
     * @return bool
     */
    private function isCurrentLineLastLineInDocument()
    {
        return ($this->offset + $this->currentLineNb) >= ($this->totalNumberOfLines - 1);
    }

}
