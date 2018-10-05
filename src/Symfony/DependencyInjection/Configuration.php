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

namespace Ruler\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ruler');

        $rootNode
            ->children()
                ->arrayNode('rules')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('default')
                                ->defaultNull()
                            ->end()
                            ->arrayNode('conditions')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('expression')
                                        ->end()
                                        ->arrayNode('value')
                                            ->beforeNormalization()
                                                ->ifString()
                                                    ->then(function ($value) {
                                                        return ['return' => $value];
                                                    })
                                                ->end()
                                            ->prototype('scalar')
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}