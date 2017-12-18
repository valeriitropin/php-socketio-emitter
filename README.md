# PHP socket.io emitter

Async implementation of socket.io emitter written in php. Built on top of [ReactPHP](https://reactphp.org/) components.

## Installation
```
composer require valeriitropin/socketio-emitter
```

## How to use

```php
use React\EventLoop\Factory as ReactFactory;
use ValeriiTropin\Socketio\Emitter;

$loop = ReactFactory::create();
$emitter = new Emitter($loop);

$promise = $emitter->to($room)->emit($event, $data)
    ->then(function () {})
    ->otherwise(function ($error) {});
```

## API
### Emitter
#### __construct(React\EventLoop\LoopInterface $loop, $options = [], ValeriiTropin\Socketio\PackerInterface $packer = null)
##### $options: 
- `key`: pub/sub events prefix (`socket.io`)
- `namespace`: socket.io namespace (`/`)
- `uri`: Redis connection string, see [docs](https://github.com/clue/php-redis-react/blob/master/README.md#createclient) (`localhost`)
- `client`: pub client

### to($room): ValeriiTropin\Socketio\Emitter

Adds room and returns current `Emitter` instance

```php
$emitter->to($room);
```

### of($namespace): ValeriiTropin\Socketio\Emitter

Creates new `Emitter` instance with given namespace.

```php
$emitter->of($namespace);
```

### emit($event, ...$args): React\Promise\Promise

Emits event with data to set rooms.

```php
$emitter->emit($event, $data)
    ->then(function () {})
    ->otherwise(function ($error) {});
```

### getLoop(): React\EventLoop\LoopInterface
 
Returns loop instance.

## Links
 * [Socket.io](https://github.com/socketio/socket.io)
 * [Socket.io Redis adapter](https://github.com/socketio/socket.io-redis)
 * [ReactPHP promises](https://reactphp.org/promise/)
 * [ReactPHP Redis](https://github.com/clue/php-redis-react)
