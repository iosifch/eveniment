<?php

namespace Eveniment\Tests;

use Eveniment\EventDispatcher;

class EventDispatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @var EventDispatcher */
    protected $dispatcher;

    public function setUp()
    {
        $this->dispatcher = new EventDispatcher();
    }

    /** @test */
    public function removeSubscriberWithLowerPriority()
    {
        $this->dispatcher->removeSubscribers();
        
        $subscriber1 = function() { return 'higher priority'; };
        $subscriber2 = function() { return 'lower priority'; };
        
        $this->dispatcher->on('foo', $subscriber1, 1);
        $this->dispatcher->on('foo', $subscriber2, 2);
        
        $this->assertCount(2, $this->dispatcher->getSubscribers('foo'));
        
        $this->dispatcher->removeSubscriber('foo', $subscriber2);

        $this->assertCount(1, $this->dispatcher->getSubscribers('foo'));
    }

    /** @test */
    public function removeSubscribersForEvent()
    {
        $this->dispatcher->removeSubscribers();
        
        $this->dispatcher->on('foo', function() {});
        $this->dispatcher->on('poo', function() {});
        
        $this->assertCount(1, $this->dispatcher->getSubscribers('foo'));
        $this->assertCount(1, $this->dispatcher->getSubscribers('poo'));
        
        $this->dispatcher->removeSubscribers('foo');
        
        $this->assertCount(0, $this->dispatcher->getSubscribers('foo'));
        $this->assertCount(1, $this->dispatcher->getSubscribers('poo'));
    }

    /** @test */
    public function callSubscriberWithOptionalParam()
    {
        $this->dispatcher->removeSubscribers();
        
        $nameFromSubscriber = null;
        $logger = new Logger();
        
        $this->dispatcher->on('foo', function($name, Logger $logger, $address = null) use (&$nameFromSubscriber) {
            $nameFromSubscriber = $name;
            $logger->offsetSet('name', $name);
        });
        $this->dispatcher->dispatch('foo', ['joe', $logger]);
        
        $this->assertEquals('joe', $nameFromSubscriber);
        $this->assertEquals('joe', $logger->offsetGet('name'));
    }

    /**
     * @test
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function subscriberIsCalledWithWrongParams()
    {
        $this->dispatcher->removeSubscribers();
        
        $nameFromSubscriber = null;
        $logger = new Logger();
        
        $this->dispatcher->on('foo', function($name, Logger $logger, $address) use (&$nameFromSubscriber) {
            $nameFromSubscriber = $name;
            $logger->offsetSet('name', $name);
        });
        
        
        $this->dispatcher->dispatch('foo', ['joe', $logger]);
    }

    /** @test */
    public function callSubscriberWithTwoParams()
    {
        $this->dispatcher->removeSubscribers();
        
        $nameFromSubscriber = null;
        $logger = new Logger();
        
        $this->dispatcher->on('foo', function($name, Logger $logger) use (&$nameFromSubscriber) {
            $nameFromSubscriber = $name;
            $logger->offsetSet('name', $name);
        });
        $this->dispatcher->dispatch('foo', ['joe', $logger]);
        
        $this->assertEquals('joe', $nameFromSubscriber);
        $this->assertEquals('joe', $logger->offsetGet('name'));
    }

    /** @test */
    public function shouldCallSubscribersByPriority()
    {
        $this->dispatcher->removeSubscribers();

        $firstCalled = null;
        $secondCalled = null;

        $this->dispatcher->on('foo', function() use (&$firstCalled) {
            $firstCalled = time();
            sleep(1);
        }, 2);

        $this->dispatcher->on('foo', function() use (&$secondCalled) {
            $secondCalled = time();
        }, 1);

        $this->dispatcher->dispatch('foo');

        $this->assertTrue($firstCalled < $secondCalled);
    }

    /** @test */
    public function removeSubscriberAfterOneUse()
    {
        $this->dispatcher->removeSubscribers();

        $callsCount = 0;
        $subscriber = function() use (&$callsCount) {
            $callsCount++;
        };

        $this->dispatcher->on('foo', $subscriber);
        $this->dispatcher->dispatch('foo');

        $this->assertEquals(1, $callsCount);

        $this->dispatcher->removeSubscriber('foo', $subscriber);

        $this->dispatcher->dispatch('foo');

        $this->assertEquals(1, $callsCount);
    }

    /** @test */
    public function shouldDispatchWithArrayParam()
    {
        $this->dispatcher->removeSubscribers();

        $logger = new Logger();

        $this->dispatcher->on('event.array_param', function(array $param) use ($logger) {
            foreach ($param as $key => $value) {
                $logger->offsetSet($key, $value);
            }
        });

        $this->dispatcher->dispatch('event.array_param', [['name' => 'iosif']]);

        $this->assertEquals($logger->offsetGet('name'), 'iosif');
    }

    /** @test */
    public function shouldDispatchWithObjectParam()
    {
        $this->dispatcher->removeSubscribers();

        $this->dispatcher->on('event.object_param', function (Logger $logger) {
            $logger->offsetSet('name', 'joe');
        });

        $objectParam = new Logger();
        $this->dispatcher->dispatch('event.object_param', [$objectParam]);

        $this->assertEquals('joe', $objectParam->offsetGet('name'));
    }

    /** @test */
    public function shouldAttachLambdaSubscriber()
    {
        $this->dispatcher->removeSubscribers();

        $this->dispatcher->on('lambda.subscriber', function() {
            return 123;
        });

        $this->assertCount(1, $this->dispatcher->getSubscribers('lambda.subscriber'));
    }

    /** @test */
    public function shouldAttachStaticFunctionSubscriber()
    {
        $this->dispatcher->removeSubscribers();

        $this->dispatcher->on('static.subscriber', ['Eveniment\Tests\Subscriber', 'onStaticCall']);

        $this->assertCount(1, $this->dispatcher->getSubscribers('static.subscriber'));
    }
}
