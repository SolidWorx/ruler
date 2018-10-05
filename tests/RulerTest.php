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

namespace Test;

use Doctrine\Common\Collections\ExpressionBuilder;
use PHPUnit\Framework\TestCase;
use Ruler\Ruler;
use Symfony\Component\ExpressionLanguage\Expression;

class RulerTest extends TestCase
{
    public function testBasicArrayRules()
    {
        $ruler = new Ruler();

        $expr = new ExpressionBuilder();

        $ruler->add($expr->eq('someField', 123), 'abc');

        $context = ['someField' => 123];

        $this->assertSame('abc', $ruler->decide($context));
    }

    public function testNestedArrayRules()
    {
        $ruler = new Ruler();

        $expr = new ExpressionBuilder();

        $ruler->add($expr->eq('[someField][other]', 'testing'), 'abc');

        $context = ['someField' => ['other' => 'testing']];

        $this->assertSame('abc', $ruler->decide($context));
    }

    public function testOrArrayRules()
    {
        $ruler = new Ruler();

        $expr = new ExpressionBuilder();

        $ruler->add($expr->orX($expr->eq('someField', 'abc'), $expr->eq('someField', 'def')), 'ghi');

        $context = ['someField' => 'def'];

        $this->assertSame('ghi', $ruler->decide($context));
    }

    public function testExpressionStringRules()
    {
        $ruler = new Ruler();

        $ruler->add('someField["other"] == "one"', 'two');

        $context = ['someField' => ['other' => 'one']];

        $this->assertSame('two', $ruler->decide($context));
    }

    public function testExpressionRules()
    {
        $ruler = new Ruler();

        $ruler->add(new Expression('someField.other == "six"'), 'seven');

        $context = ['someField' => (object) ['other' => 'six']];

        $this->assertSame('seven', $ruler->decide($context));
    }

    public function testNoRules()
    {
        $this->expectException('Ruler\Exception\InvalidRuleException');
        $this->expectExceptionMessage('No rules matched');

        $ruler = new Ruler();

        $ruler->add('someField == "abc"', 'nothing');

        $context = ['someField' => 'def'];

        $ruler->decide($context);
    }

    public function testMultipleRules()
    {
        $ruler = new Ruler();

        $ruler->add('someField == "123"', 'abc');
        $ruler->add('someField == "456"', 'DEF');

        $context = ['someField' => 456];

        $this->assertSame('DEF', $ruler->decide($context));
    }

    public function testCallback()
    {
        $ruler = new Ruler();

        $ruler->add('someField == "abc"', function () {
            return 123;
        });

        $context = ['someField' => 'abc'];

        $this->assertSame(123, $ruler->decide($context));
    }
}