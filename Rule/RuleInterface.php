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

namespace Frosit\Component\Yar\Rule;

/**
 * Interface RuleInterface
 * @package Frosit\Component\Yar\Rule
 */
interface RuleInterface
{

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param $name
     * @return mixed
     */
    public function setName($name);

    /**
     * @return mixed
     */
    public function getTags();

    /**
     * @param $tags
     * @return mixed
     */
    public function setTags($tags);

    /**
     * @param $tag
     * @return mixed
     */
    public function setTag($tag);

    /**
     * @return mixed
     */
    public function getMeta();

    /**
     * @param $meta
     * @return mixed
     */
    public function setMeta($meta);

    /**
     * @return mixed
     */
    public function getStrings();

    /**
     * @param $string
     * @return mixed
     */
    public function setStrings($string);

    /**
     * @param array $string
     * @return mixed
     */
    public function setString(array $string);

    /**
     * @return mixed
     */
    public function getConditions();

    /**
     * @param $condition
     * @return mixed
     */
    public function setConditions($condition);

    /**
     * @param $condition
     * @return mixed
     */
    public function setCondition($condition);

}
