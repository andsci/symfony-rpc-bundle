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
        $dataCacheKey = '';
        foreach ($request->getData() as $key => $item) {
            $dataCacheKey = $dataCacheKey.'|'.$key.'-'.$item;
        }

        return $server.'|'.$request->getAction().'|'.$dataCacheKey;
    }
}
