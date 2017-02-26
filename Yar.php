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

namespace Frosit\Component\Yar;

use Frosit\Component\Yar\Exception\RuntimeException;
use Frosit\Component\Yar\Parser;
use Frosit\Component\Yar\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Yar
 *
 * @package Frosit\Component\Yar
 */
class Yar
{
    /**
     * @param $input
     * @param bool $objectSupport
     * @param bool $exceptionOnInvalidType
     * @return array
     * @internal param bool $objectForMap
     */
    public static function parse($input, $objectSupport = false, $exceptionOnInvalidType = false) {
        $file = '';

        $yar = new Parser();

        // if is path to file
        if (strpos($input, "\n") === false && is_file($input)) {

            if (false === is_readable($input)) {
                throw new ParseException(sprintf('Unable to parse "%s" as the file is not readable.', $input));
            }

            $file = $yar::resolveIncludes($input);

        } elseif (strpos($input, '{')) {
            $file = $input;
        } else {
            throw new ParseException(sprintf('Unable to parse "%s" as the input could not be parsed', $input));
        }


        try {
            return $yar->parse($file, $objectSupport, $exceptionOnInvalidType);
        } catch (ParseException $e) {
            if ($file) {
                $e->setParsedFile($file);
            }

            throw $e;
        }
    }


}
