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

use Ruler\Ruler;
use Ruler\Storage\ArrayStorage;
use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Security\Core\Authorization as Security;

class RulerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->processRules($config['rules'], $container);
    }

    /**
     * @param array            $rules
     * @param ContainerBuilder $container
     *
     * @throws DependencyInjection\Exception\BadMethodCallException|DependencyInjection\Exception\InvalidArgumentException
     */
    private function processRules(array $rules, ContainerBuilder $container): void
    {
        $storageDefinition = new Definition(ArrayStorage::class);

        foreach ($rules as $name => $rule) {
            $ruleDefinition = $this->getRuleDefinition($rule);

            $container->setDefinition('ruler.rule.'.$name, $ruleDefinition);

            $storageDefinition->addMethodCall('add', [$name, $ruleDefinition]);
        }

        $container->setDefinition('ruler', $storageDefinition);
    }

    /**
     * @param array $rules
     *
     * @return Definition
     * @throws DependencyInjection\Exception\InvalidArgumentException
     */
    private function getRuleDefinition(array $rules): Definition
    {
        $definition = new Definition(Ruler::class, [$this->getExpressionLanguage()]);
        $definition->addTag('ruler.rule');

        $defaultValue = $rules['default'];

        foreach ($rules['conditions'] as $rule) {
            $value = $rule['value'];

            array_walk($value, function (&$value) {
                if ('@' === substr($value, 0, 1)) {
                    $value = new Reference(substr($value, 1));
                }
            });

            if (array_key_exists('return', $value)) {
                $value = $value['return'];
            }

            $definition->addMethodCall('add', [$rule['expression'], $value]);
        }

        if (null !== $defaultValue) {
            $definition->addMethodCall('add', ['true', $defaultValue]);
        }

        $definition->addMethodCall('setContainer', [new Reference('service_container')]);

        return $definition;
    }

    /**
     * @return Definition
     */
    private function getExpressionLanguage(): Definition
    {
        $providers = [
            new Definition(DependencyInjection\ExpressionLanguageProvider::class),
            new Definition(Security\ExpressionLanguageProvider::class),
        ];

        return new Definition(ExpressionLanguage::class, [null, $providers]);
    }
}
