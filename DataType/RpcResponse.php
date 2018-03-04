<?php

namespace RpcBundle\DataType;

use RpcBundle\Constant\RpcResponseCode;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RpcResponse.
 */
class RpcResponse
{
    /**
     * @var array
     */
    private $data = [];
    /**
     * @var array
     */
    private $errors = [];
    /**
     * @var int
     */
    private $status;

    /**
     * @param array $errors
     *
     * @return RpcResponse
     */
    public static function createFailure($errors = []) :RpcResponse
    {
        return (new self())
            ->setStatus(RpcResponseCode::FAILURE)
            ->setErrors($errors);
    }

    /**
     * @param array|object $data
     *
     * @return RpcResponse
     */
    public static function createSuccess($data = []) :RpcResponse
    {
        return (new self())
            ->setStatus(RpcResponseCode::SUCCESS)
            ->setData($data);
    }

    /**
     * @param string $error
     *
     * @return $this
     */
    public function addError(string $error)
    {
        $this->errors[] = $error;

        return $this;
    }

    /**
     * @return array|object
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array|object $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     *
     * @return $this
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus() : int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus(int $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return JsonResponse
     */
    public function toJsonResponse() : JsonResponse
    {
        if (RpcResponseCode::SUCCESS === $this->status) {
            return JsonResponse::create($this->data);
        }

        return JsonResponse::create($this->errors, Response::HTTP_BAD_REQUEST);
    }
}