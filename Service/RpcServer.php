<?php

namespace RpcBundle\Service;

use OldSound\RabbitMqBundle\RabbitMq\RpcClient;
use RpcBundle\Constant\RpcClientEvents;
use RpcBundle\DataType\RpcRequest;
use RpcBundle\DataType\RpcResponse;
use RpcBundle\Event\RpcClientCallEvent;
use RpcBundle\Utils\RpcCache;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class RpcServer.
 */
class RpcServer
{
    /**
     * @var AdapterInterface
     */
    private $cache;
    /**
     * @var string
     */
    private $correlationId;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var RpcResponseToMessageTransformer
     */
    private $responseTransformer;
    /**
     * @var RpcClient
     */
    private $rpcClient;
    /**
     * @var string
     */
    private $server;

    /**
     * ProductClient constructor.
     *
     * @param RpcClient                       $rpcClient
     * @param string                          $server
     * @param RpcResponseToMessageTransformer $responseTransformer
     * @param EventDispatcherInterface        $dispatcher
     */
    public function __construct(RpcClient $rpcClient, string $server, RpcResponseToMessageTransformer $responseTransformer, EventDispatcherInterface $dispatcher, AdapterInterface $cache)
    {
        $this->rpcClient = $rpcClient;
        $this->server = $server;
        $this->correlationId = $this->server.'_'.crc32(microtime());
        $this->responseTransformer = $responseTransformer;
        $this->cache = $cache;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param RpcRequest $request
     * @param bool       $useCache
     * @param int        $cacheTTL
     *
     * @return RpcResponse
     */
    public function call(RpcRequest $request, bool $useCache = false, int $cacheTTL = 60) : RpcResponse
    {
        try {
            $responseString = $this->getResponse($request, $useCache, $cacheTTL);
            $response = $this->responseTransformer->reverseTransform($responseString);

            $eventName = RpcClientEvents::POST_CALL.'.'.$request->getAction();
            $this->dispatcher->dispatch($eventName, new RpcClientCallEvent($this->server, $request, $response));

            return $response;
        } catch (\Exception $exception) {
            return RpcResponse::createFailure([$exception->getMessage()]);
        }
    }

    /**
     * @param RpcRequest $request
     *
     * @return mixed
     *
     * @throws \Exception
     */
    private function doCall(RpcRequest $request)
    {
        $this->rpcClient->addRequest(serialize($request), $this->server, $this->correlationId, null, 60);
        $reply = $this->rpcClient->getReplies();
        if (!isset($reply[$this->correlationId])) {
            throw new \Exception(
                sprintf('RPC call response does not contain correlation id [%].', $this->correlationId)
            );
        }

        return $reply[$this->correlationId];
    }

    /**
     * @param RpcRequest $request
     * @param bool       $useCache
     * @param int        $cacheTTL
     *
     * @return string
     *
     * @throws \Exception
     */
    private function getResponse(RpcRequest $request, bool $useCache = false, int $cacheTTL = 60) : string
    {
        if ($useCache) {
            $cacheKey = RpcCache::getCacheKey($this->server, $request);
            $cacheItem = $this->cache->getItem($cacheKey);
            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }
            $responseString = $this->doCall($request);
            $cacheItem
                ->set($responseString)
                ->expiresAfter($cacheTTL);
            $this->cache->save($cacheItem);

            return $responseString;
        }

        return $this->doCall($request);
    }
}
