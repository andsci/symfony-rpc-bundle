<?php

namespace RpcBundle;

use RpcBundle\Compiler\RpcActionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class RpcBundle.
 */
class RpcBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RpcActionPass());
    }
}