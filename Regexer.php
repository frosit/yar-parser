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
 * Info
 *
 * Some general info about regexing
 *
 * == Strings
 * - can be encapsulated by "string", /regex string/, {hex}
 */

// == regex more ==
//var blockComments = @"/\*(.*?)\*/";
//var lineComments = @"//(.*?)\r?\n";
//var strings = @"""((\\[^\n]|[^""\n])*)""";
//var verbatimStrings = @"@(""[^""]*"")+";
// (?<string>^\W.\s*(?<name>.+)\s=\s(?<value>.+)) ---- all ines
// match block ---- meta:\n(?<meta>(?<line>.\B)[^:]*)strings:
// /\*.*?\*/ // block comments

namespace Frosit\Component\Yar;

/**
 * Class Regexer
 * @package Frosit\Component\Yar
 */
class Regexer
{

    // ( "((\\[^\n]|[^""\n])*)"| \/((\\[^\n]|[^""\n])*)\/)
    /**
     * @param $str
     * @return bool
     */
    public static function MatchStrings($str)
    {
        $re = '/(?P<name>\$.*) = (?P<string>\/(.+)\/|"(.+)")\s*\n/';
        preg_match_all($re, $str, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        return $matches ?: false;
    }

    /**
     * Matches strings, encapsulated but non specific.
     * - a bad syntax can break encapsulation " string }  broken hre "
     * @param $str
     * @return array|bool
     */
    public static function matchStringsEncapsulatedNonSpecific($str)
    {
        $re = '/\$(?P<name>.*) = (?<value>(?>{|\/|").*(?>}|\/|"))((?<tags>.+)(?>\s)|(?>\n))/';

        preg_match_all($re, $str, $matches, PREG_SET_ORDER);

        if (count($matches) > 0) {
            return $matches;
        } else {
            return false;
        }
    }

    /**
     * @todo improve, see above
     * @param $str
     * @return bool
     */
    public static function matchRuleNames($str)
    {
        $re = '/rule (?P<rule>.*) [\n|{]/';
        preg_match_all($re, $str, $matches,PREG_SET_ORDER);

        $x =2;
        return $matches;
    }

    /**
     * Matches each rule and their content
     * @todo inline rules
     * @param $str
     * @return array|bool
     */
    public static function MatchRulesWithContent($str)
    {
        /**        $re = '/(?P<rule>rule (?<head>\S+).*(?>{|\n{)(?P<body>[\s\S]*?)\n})/'; */
        $re = '/(?P<rule>rule (?<head>\S+.*)(?>{|\n{)(?P<body>[\s\S]*?)\n})/';

        preg_match_all($re, $str, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        if (count($matches) > 0) {
            return $matches;
        } else {
            return false;
        }
    }

    /**
     * Hopefully only matches meta names and conditions due to not accepting $
     * @param $str
     * @return bool|array
     */
    public static function matchMeta($str)
    {
        $re = '/(?>^\W.\s*(?<name>[^\W]+)\s=\s(?<value>.+))/m';

        preg_match_all($re, $str, $matches, PREG_SET_ORDER);

        if (count($matches) > 0) {
            return $matches;
        } else {
            return false;
        }

    }

    /**
     * Remove Block Comments
     * @todo improve
     * @param $str
     * @return mixed
     */
    public static function removeBlockComments($str)
    {
        // \/\*[^*]*\*+([^\/][^*]*\*+)*\/
        // \/\*(?!(")|("))(.*?)\*\/ -- can exlude something


        $re = '/(\s+)\/\*([^\/]*)\*\/(\s+)/s';

        $str = preg_replace($re, '', $str);

        return $str;
    }

    /**
     * Removed a codeblock but requires the indentation upfront as a hotfix
     * @todo fix codeblock removal
     * @param $str
     * @param int $indentation
     * @return mixed
     */
    public static function removeBlockCommentIndentSafe($str, $indentation = 4)
    {
        $re = '/\n\s{' . $indentation . '}(\/\*[^*]*\*+([^\/][^*]*\*+)*\/)/U';
        /**
         * @note untested
         */
        $str = preg_replace($re, str_repeat(' ', $indentation), $str);

        return $str;
    }

    /**
     * @param $str
     */
    public static function removeSingleLineComments($str)
    {


    }

    /**
     * @param $str
     * @return bool
     */
    public static function splitLinesByPosition($str)
    {
        $re = '/^(?<value>.*)$/m';

        preg_match_all($re, $str, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        if (count($matches) > 0) {
            return $matches;
        } else {
            return false;
        }
    }

    /**
     * Matches conditions
     * @param $str
     * @param bool|int $indentation
     * @return array|bool
     */
    public static function matchConditions($str, $indentation = false)
    {
        $indentation = !$indentation ? '' : '{' . $indentation . '}';
        $re = '/\s' . $indentation . 'condition:\s*(?<conditions>.*)/';

        preg_match_all($re, $str, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        if (count($matches) > 0) {
            return $matches;
        } else {
            return false;
        }
    }

}