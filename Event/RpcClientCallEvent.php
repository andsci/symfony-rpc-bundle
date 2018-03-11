<?php

namespace RpcBundle\Event;

use RpcBundle\DataType\RpcRequest;
use RpcBundle\DataType\RpcResponse;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class RpcClientCallEvent.
 */
class RpcClientCallEvent extends Event
{
    /**
     * @var RpcRequest
     */
    private $request;
    /**
     * @var RpcResponse
     */
    private $response;
    /**
     * @var string
     */
    private $server;

    /**
     * RpcClientCallEvent constructor.
     *
     * @param string      $server
     * @param RpcRequest  $request
     * @param RpcResponse $response\
     */
    public function __construct(string $server, RpcRequest $request, RpcResponse $response)
    {
        $this->server = $server;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return RpcRequest
     */
    public function getRequest() : RpcRequest
    {
        return $this->request;
    }

    /**
     * @return RpcResponse
     */
    public function getResponse() : RpcResponse
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getServer() : string
    {
        return $this->server;
    }
}
