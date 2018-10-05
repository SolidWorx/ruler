<?php

/*
 * This file is part of the ruler project.
 *
 * @author     SolidWorx <open-source@solidworx.co>
 * @copyright  Copyright (c)
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ruler\Storage;

use Ruler\Ruler;

class ArrayStorage implements StorageInterface
{
    /**
     * @var array
     */
    private $rules = [];

    /**
     * {@inheritdoc}
     */
    public function add($name, Ruler $ruler)
    {
        if (isset($this->rules[$name])) {
            throw new \Exception(sprintf('Rule "%s" already exist in storage'));
        }

        $this->rules[$name] = $ruler;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        if (!isset($this->rules[$name])) {
            throw new \Exception(sprintf('Rule "%s" does not exist in storage'));
        }

        return $this->rules[$name];
    }
}