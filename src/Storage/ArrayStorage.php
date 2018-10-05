<?php

declare(strict_types=1);

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

use Ruler\Exception\InvalidRuleException;
use Ruler\Ruler;

class ArrayStorage implements StorageInterface
{
    /**
     * @var Ruler[]
     */
    private $rules = [];

    /**
     * @throws InvalidRuleException
     */
    public function add(string $name, Ruler $ruler): void
    {
        if (isset($this->rules[$name])) {
            throw new InvalidRuleException(sprintf('Rule "%s" already exist in storage'));
        }

        $this->rules[$name] = $ruler;
    }

    /**
     * @throws InvalidRuleException
     */
    public function get(string $name): Ruler
    {
        if (!isset($this->rules[$name])) {
            throw new InvalidRuleException(sprintf('Rule "%s" does not exist in storage'));
        }

        return $this->rules[$name];
    }
}