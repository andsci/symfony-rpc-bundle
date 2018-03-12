<?php

namespace RpcBundle\Service;

use RpcBundle\DataType\RpcRequest;
use RpcBundle\DataType\RpcResponse;

/**
 * Interface RpcClientInterface.
 */
interface RpcClientInterface
{
    /**
     * @param RpcRequest $request
     * @param bool       $useCache
     * @param int        $cacheTTL
     *
     * @return RpcResponse
     */
    public function call(RpcRequest $request, bool $useCache = false, int $cacheTTL = 60) : RpcResponse;
}