<?php

declare(strict_types=1);

namespace Trikoder\Bundle\OAuth2Bundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Trikoder\Bundle\OAuth2Bundle\Controller\AuthorizationController;
use Trikoder\Bundle\OAuth2Bundle\League\Repository\ScopeRepository;
use Trikoder\Bundle\OAuth2Bundle\League\Repository\UserRepository;
use Trikoder\Bundle\OAuth2Bundle\Service\BCEventDispatcher;

class EventDispatcherCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->has(EventDispatcherInterface::class)) {
            return;
        }

        // Register a new service
        $definition = new Definition(BCEventDispatcher::class);
        $definition->addArgument(new Reference(\Symfony\Component\EventDispatcher\EventDispatcherInterface::class));
        $container->setDefinition(BCEventDispatcher::class, $definition);

        // Use our new service
        $container->getDefinition(ScopeRepository::class)
            ->replaceArgument(3, new Reference(BCEventDispatcher::class));
        $container->getDefinition(UserRepository::class)
            ->replaceArgument(1, new Reference(BCEventDispatcher::class));
        $container->getDefinition(AuthorizationController::class)
            ->replaceArgument(1, new Reference(BCEventDispatcher::class));
    }
}
