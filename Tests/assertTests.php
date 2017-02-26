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

/**
 * {@inheritDoc}
 */
class YarTest extends \PHPUnit_Framework_TestCase
{

    protected $yar;

    protected function setUp()
    {
        $this->yar = new Yar();
    }

    protected function tearDown()
    {
        $this->yar = null;
    }

    /**
     * @param $rule
     * @param string $message
     */
    public static function assertTrueRule($rule, $message = '')
    {
        $rule = Yar::parse($rule);

        if ($message = '') {
            $message = 'Failed asserting True for rule : '.$rule;
        }

        self::assertThat($rule, self::isTrue(), $message);
    }

    /**
     * @param $rule
     * @param string $message
     */
    public static function assertFalseRule($rule, $message = '')
    {
        $rule = Yar::parse($rule);

        if ($message = '') {
            $message = 'Failed asserting False for rule : '.$rule;
        }

        self::assertThat($rule, self::isFalse(), $message);
    }


    public function testIncludes()
    {

        self::assertTrueRule("rule test { condition: true }");


        self::assertFalseRule("rule test {fds condition: true }");

    }

}