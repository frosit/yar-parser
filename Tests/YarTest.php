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

namespace Frosit\Component\Yar\Tests;

use Frosit\Component\Yar\Yar;

class YarTest extends \PHPUnit_Framework_TestCase
{

    protected $yar;

    protected function setUp()
    {
        $this->yar = new Yar();
    }

    protected function tearDown()
    {
        $this->yar;
    }

    /**
     * @param $rule
     * @param string $message
     */
    public static function assertTrueRule($rule, $message = '')
    {
        $rule = Yar::parse($rule);

        self::assertThat($rule, self::isTrue(), $message);


    }

    public static function assertFalseRule($rule, $message = '')
    {
        $rule = Yar::parse($rule);

        self::assertThat($rule, self::isFalse(), $message);


    }


    public function testIncludes()
    {
        $yar = new Yar();
        $file = __DIR__.'Fixtures/include.yar';

//        $x = $yar::parse($file);
//        $this->assertGreaterThan(0, $this->count($x), 'higher');


        self::assertTrueRule("rule test { condition: true }");


        self::assertTrueRule("rule test {fds condition: true }");


    }

    public function testAsserts()
    {
        self::assertTrueRule(
            "rule test { condition: true }"
        );

        self::assertTrueRule(
            "rule test { condition: true or false }"
        );

        self::assertTrueRule(
            "rule test { condition: true and true }"
        );

        self::assertTrueRule(
            "rule test { condition: 0x1 and 0x2}"
        );

        self::assertFalseRule(
            "rule test { condition: false }"
        );

        self::assertFalseRule(
            "rule test { condition: true and false }"
        );

        self::assertFalseRule(
            "rule test { condition: false or false }"
        );
    }


}