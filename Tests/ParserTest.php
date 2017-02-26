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

namespace Frosit\Component\Yar\Tests;

use Frosit\Component\Yar\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{

    protected $parser;

    protected function setUp()
    {
        $this->parser = new Parser();
    }

    protected function tearDown()
    {
        $this->parser = null;
    }

//
//    public function testResolveIncludes()
//    {
//        $parser = new Parser();
//        $file = __DIR__.'/Fixtures/include.yar';
//        $content = file_get_contents($file);
//
//        $rules = $parser::resolveIncludes($file);
//
//        $this->assertStringNotEqualsFile($content,$rules,'not same');
//    }

}