<?php

namespace RpcBundle\DataType;

use RpcBundle\Exception\RpcRequestDataMissingException;

/**
 * Class RpcRequest.
 */
class RpcRequest
{
    /**
     * @var string
     */
    private $action;
    /**
     * @var array
     */
    private $data;

    /**
     * RpcRequest constructor.
     *
     * @param string $action
     * @param array  $data
     */
    public function __construct(string $action, array $data = [])
    {
        $this->action = $action;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getAction() : string
    {
        return $this->action;
    }

    /**
     * @param string $action
     *
     * @return $this
     */
    public function setAction(string $action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return array
     */
    public function getData() : array
    {
        return $this->data;
    }

    /**
     * @param string $key
     *
     * @return mixed
     *
     * @throws RpcRequestDataMissingException
     */
    public function getDataItem(string $key)
    {
        if (null !== $key) {
            if (array_key_exists($key, $this->data)) {
                return $this->data[$key];
            }
            throw RpcRequestDataMissingException::createForKey($key);
        }
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }
}
