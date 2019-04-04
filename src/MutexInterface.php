<?php

namespace Pascal\TaskScheduler;

interface MutexInterface
{

    /**
     * @param MutexKey $key
     *
     * @return bool
     */
    public function create(MutexKey $key): bool;

    /**
     * @param MutexKey $key
     *
     * @return bool
     */
    public function exists(MutexKey $key): bool;

    /**
     * @param MutexKey $key
     *
     * @return bool
     */
    public function release(MutexKey $key): bool;
}
