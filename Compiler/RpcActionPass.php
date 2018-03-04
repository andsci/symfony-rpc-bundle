<?php

namespace RpcBundle\Compiler;

use Doctrine\Common\Annotations\AnnotationReader;
use RpcBundle\Annotation\RpcAction;
use RpcBundle\Annotation\RpcServer;
use RpcBundle\Constant\CacheKey;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class RpcActionPass.
 */
class RpcActionPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $annotationReader = new AnnotationReader();
        $routes = [];
        foreach ($container->findTaggedServiceIds('controller.rpc') as $id => $tags) {
            $builderDefinition = $container->getDefinition($id);
            $reflectedClass = new \ReflectionClass($builderDefinition->getClass());
            /** @var RpcServer $classAnnotation */
            $classAnnotation = $annotationReader->getClassAnnotation($reflectedClass, RpcServer::class);
            $routes[$classAnnotation->service] = [];
            $actions = [];
            foreach ($reflectedClass->getMethods() as $method) {
                /** @var RpcAction $methodAnnotation */
                $methodAnnotation = $annotationReader->getMethodAnnotation($method, RpcAction::class);
                if ($methodAnnotation instanceof RpcAction) {
                    $actions[$methodAnnotation->name] = $method->getName();
                }
            }
            $routes[$classAnnotation->service][$builderDefinition->getClass()] = $actions;
        }

        $this->cacheRoutes($routes, $container);
    }

    /**
     * @param array            $routes
     * @param ContainerBuilder $container
     */
    private function cacheRoutes(array $routes,ContainerBuilder $container)
    {
        $cache = new FilesystemAdapter('rpc', 0, $container->getParameter('kernel.cache_dir'));
        $cacheItem = $cache->getItem(CacheKey::ROUTES);
        $cacheItem->set($routes);
        $cache->save($cacheItem);
    }
}