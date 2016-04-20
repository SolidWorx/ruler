<?php
/*
 * This file is part of the ruler project.
 *
 * @author     Pierre du Plessis <pdples@gmail.com>
 * @copyright  Copyright (c) 2016
 */

namespace Ruler;

use Ruler\Exception\InvalidRuleException;
use Ruler\Visitor\ClosureExpressionVisitor;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class Ruler
{
    /**
     * @var ExpressionLanguage
     */
    private $el;

    /**
     * @param ExpressionLanguage $el
     */
    public function __construct(ExpressionLanguage $el = null)
    {
        $this->el = $el;
    }

    /**
     * @var Rule[]
     */
    private $rules;

    /**
     * @param \Doctrine\Common\Collections\Expr\Expression|Expression|string $expression
     * @param mixed                                                          $return
     *
     * @return Rule
     */
    public function add($expression, $return)
    {
        if (is_string($expression)) {
            $expression = new Expression($expression);
        }

        return $this->rules[] = new Rule($expression, $return);
    }

    /**
     * @param mixed $context
     *
     * @return mixed
     * @throws \Exception
     */
    public function decide($context)
    {
        $visitor = new ClosureExpressionVisitor;

        foreach ($this->rules as $rule) {
            $expression = $rule->getExpression();

            if ($expression instanceof Expression) {
                if (null === $this->el) {
                    $this->el = new ExpressionLanguage();
                }

                $response = $this->el->evaluate($expression, $context);
            } else {
                $filter = $visitor->dispatch($expression);
                $response = $filter($context);
            }

            if ($response) {
                $return = $rule->getReturn();

                if (is_callable($return)) {
                    return call_user_func($return, $context);
                }

                return $return;
            }
        }

        throw new InvalidRuleException('No rules matched');
    }
}