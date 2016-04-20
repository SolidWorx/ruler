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
                ->useAttributeAsKey('name')
                ->prototype('array')
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
                                    ->prototype('scalar')->end()
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