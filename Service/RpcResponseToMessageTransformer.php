<?php

namespace RpcBundle\Service;

use JMS\Serializer\SerializerInterface;
use RpcBundle\DataType\RpcResponse;

/**
 * Class RpcResponseToMessageTransformer.
 */
class RpcResponseToMessageTransformer
{
    const FORMAT = 'json';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * RpcResponseToMessageTransformer constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param string $messageString
     * @param string $dataType
     *
     * @return RpcResponse
     */
    public function reverseTransform(string $messageString, string $dataType = 'array') : RpcResponse
    {
        $messageData = json_decode($messageString, true);
        $responseBody = json_decode($messageData['body'], true);
        $response = (new RpcResponse())
            ->setStatus($responseBody['status'])
            ->setData($this->serializer->deserialize($responseBody['data'], $dataType, self::FORMAT));
        foreach ($messageData['errors'] as $error) {
            $response->addError($error);
        }

        return $response;
    }

    /**
     * @param RpcResponse $response
     *
     * @return string
     */
    public function transform(RpcResponse $response) : string
    {
        return json_encode([
            'body' => json_encode(
                [
                    'status' => $response->getStatus(),
                    'data' => $this->serializer->serialize($response->getData(), self::FORMAT),
                ]
            ),
            'errors' => $response->getErrors(),
        ]);
    }
}