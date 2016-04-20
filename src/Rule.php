<?php
/*
 * This file is part of the ruler project.
 *
 * @author     Pierre du Plessis <pdples@gmail.com>
 * @copyright  Copyright (c) 2016
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ruler;

final class Rule
{
    /**
     * @var mixed
     */
    private $expression;

    /**
     * @var mixed
     */
    private $return;

    /**
     * Rule constructor.
     *
     * @param mixed $expression
     * @param mixed $return
     */
    public function __construct($expression, $return)
    {
        $this->expression = $expression;
        $this->return = $return;
    }

    /**
     * @return mixed
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @return mixed
     */
    public function getReturn()
    {
        return $this->return;
    }
}