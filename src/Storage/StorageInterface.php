<?php
/*
 * This file is part of the ruler project.
 *
 * @author     Pierre du Plessis <pdples@gmail.com>
 * @copyright  Copyright (c) 2016
 */

namespace Ruler\Storage;

use Ruler\Ruler;

interface StorageInterface
{
    /**
     * @param string $name
     * @param Ruler  $ruler
     *
     * @return mixed
     */
    public function add($name, Ruler $ruler);

    /**
     * @param string $name
     *
     * @return Ruler
     */
    public function get($name);
}