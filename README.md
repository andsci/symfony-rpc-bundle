# Symfony rpc bundle

### Controller definition

Defining controller server

```php
    /**
     * Class ProductController.
     * @RpcServer(service="App\Service\Server\ProductServer")
     */
    class ProductController 
    {
    }
```

Defining controller actions

```php
    /**
     * @RpcAction(name="create-product")
     * @param array $data
     *
     * @return RpcResponse
     */
    public function createProduct(array $data) : RpcResponse
    {
    }
```

### Calling action

```php
    $actionCaller->call($request->getAction(), $server, $request->getData());
```