<?php

namespace RpcBundle\Annotation;

/**
 * Class RpcAction.
 * @Annotation
 * @Target({"METHOD"})
 *
 */
class RpcAction
{
    /** @var string */
    public $name;
}