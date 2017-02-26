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
 * @package     Yar Parser
 * @author      Fabio Ros <f.ros@gdprproof.com> - <fabio@frosit.nl>
 * @copyright   Copyright (c) 2016 Fabio Ros - GDPRProof B.V.
 * @license     https://opensource.org/licenses/AGPL-3.0 GNU Affero General Public License version 3
 *
 */

/**
 * Remove multiline comments : \/\*[\s\S]*?\*\/|([^:]|^)\/\/.*$ || break rule md5_4c4b3d4ba5bce7191a5138efa2468679
 */

namespace Frosit\Component\Yar;

use Frosit\Component\Yar\Exception\ParseException;
use Frosit\Component\Yar\Rule\Parser as RuleParser;

/**
 * Class Parser
 * @package Frosit\Yarparser
 */
class Parser
{
    public $originalContent;
    public $parseByRegex = false;

    /**
     * Cleans multiline comments at start with regex
     * @note, may break some strings
     * @var bool
     */
    public $cleanMultilineCommentsAtStart = false;

    /**
     *
     * @var bool
     */
    public $cleanSingleLineCommentsAtStart = true;

    public function __construct()
    {

    }

    /**
     * @param $content
     * @param $exceptionOnInvalidType
     * @return array
     */
    public function parse($content, $objectSupport, $exceptionOnInvalidType)
    {

        if (!preg_match('//u', $content)) {
            throw new ParseException('The YAML value does not appear to be valid UTF-8.');
        }

        // set original content so we can refer back
        $this->originalContent = $content;


        /**
         * @note when experiencing issue, change the cleancontent function
         */
        $content = $this->cleanContent($content);

        // @todo maybe fix encodings
        // $content = $this->fixEcondings or something

        $rules = array();


        /**
         * Strip rules
         * We want each rule [head>[name] : [tags]]{ <body> }
         * This should also detect inline rules!
         */


        if ($ruleWithContent = Regexer::matchRuleNames($content) === 1) {
            $rulesWithContent[] = $content;

        } elseif ($rulesWithContent = Regexer::MatchRulesWithContent($content)) {

        } else {
            $rulesWithContent = false;
        }


        if ($rulesWithContent) {
            foreach ($rulesWithContent as $ruleWithContent) { // iterate each rule

                $parser = new RuleParser();
                $rule = $parser->parse($ruleWithContent);
                if (!$objectSupport) {
                    $rule = $rule->asArray();
                }
                $rules[] = $rule;

            }
        }

        return $rules;
    }

    /**
     * Cleans out unnecessary content
     * @todo begin carefull
     * @param $content
     * @return mixed
     */
    public function cleanContent($content)
    {

        /**
         * Stripts out multiline comments
         * @todo should respect quoted string
         */
        if ($this->cleanMultilineCommentsAtStart) {
            $content = preg_replace('!/\*.*?\*/!s', '', $content);
        }

//        $content = Regexer::removeBlockComments($content);

        // Strip out empty lines
        if ($this->cleanSingleLineCommentsAtStart) {
            $content = preg_replace('/\n\s*\n/', "\n", $content);
        }


        return $content;
    }


    /**
     * Resolves includes before parsing
     * @param $input
     * @return mixed
     * @internal param $content
     * @internal param $path
     */
    public static function resolveIncludes($input)
    {
        $path = realpath($input);
        $content = file_get_contents($input);

        $re = "/include \"(?P<name>.*.yar)\"/";
//        $re = '/^include "(?P<name>.*.yar)"/m';
        preg_match_all(
            $re,
            $content,
            $matches,
            PREG_SET_ORDER
        );
        if (count($matches)) { // if more than one match

            foreach ($matches as $match) {

                $replace = $match['name'];
                if (substr($replace, 0, 1) === DIRECTORY_SEPARATOR) {
                    $path = $replace;
                } else {
                    $parts = explode("/", $path);
                    array_pop($parts);
                    array_push($parts, $replace);
                    $path = implode("/", $parts);
                }

                if (is_file($path)) {
                    $matchContent = file_get_contents($path);
                    $content = str_replace($match[0], $matchContent, $content);
                } else {
                    throw new ParseException('Include could not be found.');
                }
            }
        }

        return $content;
    }

}