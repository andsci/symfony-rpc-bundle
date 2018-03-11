<?php

namespace RpcBundle\Utils;

use RpcBundle\DataType\RpcRequest;

/**
 * Class RpcCache.
 */
class RpcCache
{
    /**
     * @param string     $server
     * @param RpcRequest $request
     *
     * @return string
     */
    public static function getCacheKey(string $server, RpcRequest $request) : string
    {
        return $server.':'.$request->getAction().':'.json_encode($request->getData());
    }
}