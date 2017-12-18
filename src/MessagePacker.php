<?php

namespace ValeriiTropin\Socketio;

use MessagePack\Packer;

class MessagePacker implements PackerInterface
{
    protected $packer;

    public function __construct()
    {
        $this->packer = new Packer();
    }

    public function pack($data)
    {
        return $this->packer->pack($data);
    }


}