<?php

namespace RpcBundle\Service;

use RpcBundle\Constant\CacheKey;
use RpcBundle\DataType\RpcResponse;
use RpcBundle\Exception\RpcActionNotFoundException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AnnotationRpcActionCaller.
 */
class AnnotationRpcActionCaller implements RpcActionCallerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * AnnotationRpcActionCaller constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function call(string $action, string $server, array $data = []) : RpcResponse
    {
        $routes = $this->getRoutes();
        if (null === $routes || !array_key_exists($server, $routes)) {
            throw new RpcActionNotFoundException(sprintf('No actions found for server "%s"', $server));
        }

        foreach ($routes[$server] as $controllerServiceName => $controllerActions) {
            if (array_key_exists($action, $controllerActions)) {
                $controller = $this->container->get($controllerServiceName);
                return $controller->{$controllerActions[$action]}($data);
            }
        }

        throw new RpcActionNotFoundException(sprintf('Action not found "%s"', $action));
    }

    /**
     * @return array|null
     */
    private function getRoutes() : ?array
    {
        $cache = new FilesystemAdapter('rpc', 0, $this->container->getParameter('kernel.cache_dir'));
        $cacheItem = $cache->getItem(CacheKey::ROUTES);

        return $cacheItem->get();
    }
}