<?php

namespace Pascal\TaskScheduler;

use Symfony\Component\Lock\Exception\LockAcquiringException;
use Symfony\Component\Lock\Exception\LockConflictedException;
use Symfony\Component\Lock\Exception\LockReleasingException;
use Symfony\Component\Lock\StoreInterface;

class MutexAdapter implements MutexInterface
{

    /**
     * @var \Symfony\Component\Lock\StoreInterface
     */
    private $store;

    /**
     * @param \Symfony\Component\Lock\StoreInterface $store +
     */
    public function __construct(StoreInterface $store)
    {
        $this->store = $store;
    }

    /**
     * @param \Pascal\TaskScheduler\MutexKey $key
     *
     * @return bool
     */
    public function create(MutexKey $key): bool
    {
        try {
            $this->store->save($key->getKey());
        } catch (LockAcquiringException $lockAcquiringException) {
            return false;
        } catch (LockConflictedException $lockConflictedException) {
            return false;
        }

        return true;
    }

    /**
     * @param \Pascal\TaskScheduler\MutexKey $key
     *
     * @return bool
     */
    public function exists(MutexKey $key): bool
    {
        return $this->store->exists($key->getKey());
    }

    /**
     * @param \Pascal\TaskScheduler\MutexKey $key
     *
     * @return bool
     */
    public function release(MutexKey $key): bool
    {
        try {
            $this->store->delete($key->getKey());
        } catch (LockReleasingException $lockAcquiringException) {
            return false;
        }

        return true;
    }
}
