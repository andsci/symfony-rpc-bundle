<?php

namespace RpcBundle\Exception;

/**
 * Class RpcRequestDataMissingException.
 */
class RpcRequestDataMissingException extends \Exception
{
    /**
     * @param string $missingKey
     *
     * @return RpcRequestDataMissingException
     */
    public static function createForKey(string $missingKey)
    {
        return new self(sprintf('Request data does not contain key "%s"', $missingKey));
    }
}