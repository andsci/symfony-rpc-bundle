# Symfony rpc bundle


### Usage

#### Create server

Create server class

```php
<?php

use RpcBundle\Service\AbstractRpcServer;

/**
 * Class ProductServer.
 */
class ProductServer extends AbstractRpcServer
{
}
```
Create controller

```php
<?php

use RpcBundle\Annotation\RpcAction;
use RpcBundle\Annotation\RpcServer;
use RpcBundle\DataType\RpcResponse;

/**
 * Class ProductController.
 * @RpcServer(service="App\Service\Server\ProductServer")
 */
class ProductController 
{
    /**
     * @RpcAction(name="create-product")
     * @param array $data
     *
     * @return RpcResponse
     */
    public function createProduct(array $data) : RpcResponse
    {
        return RpcResponse::createSuccess($product);
    }
}
```


#### Create server client

Create client service

```yaml
    rpc.client.product:
        class: RpcBundle\Service\RpcClient
        arguments:
            - '@old_sound_rabbit_mq.products_client_rpc'
            - 'product_server'
```

Call server action

```php
<?php

use RpcBundle\DataType\RpcRequest;
use RpcBundle\DataType\RpcResponse;
use RpcBundle\Service\RpcClientInterface;

/**
 * Class ProductService.
 */
class ProductService 
{
    
    /**
     * @var RpcClientInterface
     */
    private $productClient;
    
    /**
     * @param array $data
     *
     * @return RpcResponse
     */
    public function createProduct(array $data = []) : RpcResponse
    {
        $request = new RpcRequest('create-product', $data);

        return  $this->productClient->call($request);
    }
}
```