<?php

namespace ValeriiTropin\Socketio;

use Clue\React\Redis\Client;
use React\EventLoop\LoopInterface;
use Clue\React\Redis\Factory;
use React\Promise;
use Clue\React\Block;

class Emitter
{
    const FLAGS = [
        'json',
        'volatile',
        'broadcast',
    ];

    private $loop;
    private $uid = 'emitter';
    private $channel;
    private $prefix;
    private $namespace;
    private $packer;
    private $uri;
    private $client;
    private $rooms = [];
    private $flags = [];

    public function __construct(LoopInterface $loop, $options = [], PackerInterface $packer = null)
    {
        $this->loop = $loop;
        $this->prefix = $this->getOption($options, 'key', 'socket.io');
        $this->namespace = $this->getOption($options, 'namespace', '/');
        $this->packer = $packer ?? new MessagePacker();
        $this->channel = $this->prefix . '#' . $this->namespace . '#';
        $this->uri = $this->getOption($options, 'uri', 'localhost');
        $this->client = $this->getOption($options, 'client');

        $this->createClients($this->uri);
    }

    private function createClients($uri)
    {
        $factory = new Factory($this->loop);
        if ($this->client) {
            $promise = Promise\resolve($this->client);
        } else {
            $promise = $factory->createClient($uri);
            $promise->then(function (Client $client) {$this->client = $client;});
        }
        Block\awaitAll([$promise], $this->loop);
    }

    public function to($room)
    {
        if (!in_array($room, $this->rooms)) {
            $this->rooms[] = $room;
        }
        return $this;
    }

    public function of($namespace)
    {
        $options = [
            'key' => $this->prefix,
            'namespace' => $namespace,
        ];
        return new static($this->loop, $options);
    }

    public function emit()
    {
        $args = func_get_args();
        $packet = [
            'type' => 2,
            'data' => $args,
            'nsp' => $this->namespace,
        ];
        $opts = [
            'rooms' => $this->rooms,
            'flags' => $this->flags,
        ];
        $channel = $this->channel;
        if ($opts['rooms'] && count($opts['rooms']) === 1) {
            $channel .= $opts['rooms'][0] . '#';
        }
        $data = $this->packer->pack([$this->uid, $packet, $opts]);
        $this->rooms = $this->flags = [];
        return $this->client->publish($channel, $data);
    }

    private function getOption($options, $key, $default = null)
    {
        return isset($options[$key]) ? $options[$key] : $default;
    }

    public function getLoop()
    {
        $this->loop;
    }

    public function __call($name, $arguments)
    {
        if (in_array($name,self::FLAGS)) {
            $this->flags[$name] = true;
        }
        return $this;
    }
}
