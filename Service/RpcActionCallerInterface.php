<?php

namespace RpcBundle\Service;

use RpcBundle\DataType\RpcResponse;
use RpcBundle\Exception\RpcActionNotFoundException;

/**
 * Interface RpcActionCallerInterface.
 */
interface RpcActionCallerInterface
{
    /**
     * @param string $action
     * @param string $server
     * @param array  $data
     *
     * @return RpcResponse
     * @throws RpcActionNotFoundException
     */
    public function call(string $action, string $server, array $data = []) : RpcResponse;
}