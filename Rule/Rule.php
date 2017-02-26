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
 * Class Rule
 * @package Frosit\Component\Yar\Rule
 */
class Rule implements RuleInterface
{

    /**
     * @var
     */
    private $name;
    /**
     * @var
     */
    private $tags;
    /**
     * @var
     */
    private $meta;
    /**
     * @var
     */
    private $strings;
    /**
     * @var
     */
    private $conditions;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Rule
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param $tag
     * @return $this
     */
    public function setTag($tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * @param mixed $tags
     * @return Rule
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param mixed $meta
     * @return Rule
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStrings()
    {
        return $this->strings;
    }

    /**
     * Set a single string
     * @param array $string
     * @return bool|Rule
     */
    public function setString(array $string)
    {
        if ($string['value'] === null || $string['value'] === false) {
            return false;
        } else {
            $this->strings[] = $string;
        }

        return $this;
    }

    /**
     * @param mixed $strings
     * @return Rule
     */
    public function setStrings($strings)
    {
        $this->strings = $strings;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param $condition
     * @return $this
     */
    public function setCondition($condition)
    {
        $this->conditions[] = $condition;

        return $this;
    }

    /**
     * @param mixed $conditions
     * @return Rule
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;

        return $this;
    }

    /**
     *
     */
    public function validate()
    {

    }

    /**
     * @return array
     */
    public function asArray()
    {
        $rule = array(
            "name" => $this->getName(),
            "tags" => $this->getTags(),
            "meta" => $this->getMeta(),
            "strings" => $this->getStrings(),
            "conditions" => $this->getConditions()
        );
        return $rule;
    }

}
