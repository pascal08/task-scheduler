<?php

namespace Pascal\TaskScheduler;

use Symfony\Component\Lock\Key;

class MutexKey
{

    /**
     * @var \Symfony\Component\Lock\Key
     */
    private $key;

    /**
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = new Key($key);
    }

    /**
     * @return \Symfony\Component\Lock\Key
     */
    public function getKey(): Key
    {
        return $this->key;
    }
}
