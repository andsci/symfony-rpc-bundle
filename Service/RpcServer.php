<?php

namespace RpcBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use RpcBundle\Constant\RpcResponseCode;
use RpcBundle\DataType\RpcRequest;
use RpcBundle\DataType\RpcResponse;
use RpcBundle\Exception\RpcActionNotFoundException;

/**
 * Class RpcServer.
 */
abstract class RpcServer implements ConsumerInterface
{
    /**
     * @var RpcActionCallerInterface
     */
    private $actionCaller;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var RpcResponseToMessageTransformer
     */
    private $responseTransformer;

    /**
     * RpcServer constructor.
     *
     * @param LoggerInterface                 $logger
     * @param RpcActionCallerInterface        $actionCaller
     * @param RpcResponseToMessageTransformer $responseTransformer
     * @param EntityManagerInterface          $em
     */
    public function __construct(LoggerInterface $logger, RpcActionCallerInterface $actionCaller, RpcResponseToMessageTransformer $responseTransformer, EntityManagerInterface $em)
    {
        $this->logger = $logger;
        $this->actionCaller = $actionCaller;
        $this->responseTransformer = $responseTransformer;
        $this->em = $em;
    }

    /**
     * @param AMQPMessage $message
     *
     * @return string
     */
    public function execute(AMQPMessage $message) : string
    {
        $this->logger->info(json_encode($message));
        $request = unserialize($message->body);
        if (!$request instanceof RpcRequest) {
            throw new \InvalidArgumentException(sprintf('Message should be an instance of %s', RpcRequest::class));
        }
        $this->em->clear();
        try {
            $response = $this->actionCaller->call($request->getAction(), static::class, $request->getData());
        } catch (RpcActionNotFoundException $exception) {
            $response = (new RpcResponse())
                ->setStatus(RpcResponseCode::NOT_FOUND)
                ->addError(sprintf('Action "%s" was not found.', $request->getAction()));
        } catch (\Exception $exception) {
            $response = (new RpcResponse())
                ->setStatus(RpcResponseCode::EXCEPTION)
                ->addError($exception->getMessage());
        }

        return $this->responseTransformer->transform($response);
    }
}
