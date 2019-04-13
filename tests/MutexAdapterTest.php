<?php

namespace Pascal\TaskScheduler\Tests;

use Pascal\TaskScheduler\InMemoryStore;
use Pascal\TaskScheduler\MutexAdapter;
use Pascal\TaskScheduler\MutexCreateException;
use Pascal\TaskScheduler\MutexKey;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Lock\Exception\LockAcquiringException;
use Symfony\Component\Lock\Exception\LockConflictedException;
use Symfony\Component\Lock\Exception\LockReleasingException;
use Symfony\Component\Lock\StoreInterface;

class MutexAdapterTest extends TestCase
{

    /**
     * @test
     */
    public function it_should_return_true_if_the_mutex_exists()
    {
        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock
            ->expects($this->once())
            ->method('exists')
            ->willReturn(true);
        $mutexAdapter = new MutexAdapter($storeMock);
        $key = new MutexKey(sha1('test_mutex_key'));

        $this->assertTrue($mutexAdapter->exists($key));
    }

    /**
     * @test
     */
    public function it_should_return_false_if_the_mutex_does_not_exists()
    {
        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock
            ->expects($this->once())
            ->method('exists')
            ->willReturn(false);
        $mutexAdapter = new MutexAdapter($storeMock);
        $key = new MutexKey(sha1('test_mutex_key'));

        $this->assertFalse($mutexAdapter->exists($key));
    }

    /**
     * @test
     */
    public function it_should_return_true_if_the_mutex_was_created()
    {
        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock
            ->expects($this->once())
            ->method('save')
            ->willReturn(true);
        $mutexAdapter = new MutexAdapter($storeMock);
        $key = new MutexKey(sha1('test_mutex_key'));

        $this->assertTrue($mutexAdapter->create($key));
    }

    /**
     * @test
     */
    public function it_should_return_false_if_the_mutex_was_not_created()
    {
        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock
            ->expects($this->once())
            ->method('save')
            ->willThrowException(new LockAcquiringException);
        $mutexAdapter = new MutexAdapter($storeMock);
        $key = new MutexKey(sha1('test_mutex_key'));

        $this->assertFalse($mutexAdapter->create($key));
    }

    /**
     * @test
     */
    public function it_should_return_false_if_the_mutex_was_not_created_due_to_a_conflicting_lock()
    {
        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock
            ->expects($this->once())
            ->method('save')
            ->willThrowException(new LockConflictedException());
        $mutexAdapter = new MutexAdapter($storeMock);
        $key = new MutexKey(sha1('test_mutex_key'));

        $this->assertFalse($mutexAdapter->create($key));
    }

    /**
     * @test
     */
    public function it_should_return_true_if_the_mutex_was_released()
    {
        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock
            ->expects($this->once())
            ->method('delete')
            ->willReturn(true);
        $mutexAdapter = new MutexAdapter($storeMock);
        $key = new MutexKey(sha1('test_mutex_key'));

        $this->assertTrue($mutexAdapter->release($key));
    }

    /**
     * @test
     */
    public function it_should_return_false_if_the_mutex_was_not_released()
    {
        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock
            ->expects($this->once())
            ->method('delete')
            ->willThrowException(new LockReleasingException);
        $mutexAdapter = new MutexAdapter($storeMock);
        $key = new MutexKey(sha1('test_mutex_key'));

        $this->assertFalse($mutexAdapter->release($key));
    }
}
