<?php

namespace Eveniment;

interface EventDispatcherInterface
{
    /**
     * @param string $event
     * @param callable $subscriber
     * @param integer $index
     * @return void
     */
    public function on($event, callable $subscriber, $index = 1000);

    /**
     * @param string $event
     * @return array
     */
    public function getSubscribers($event);

    /**
     * @param string $event
     * @param mixed $params
     * @return
     */
    public function dispatch($event, array $params = []);

    /**
     * @param string $event
     * @return void
     */
    public function removeSubscribers($event = null);

    /**
     * @param string $event
     * @param callable $subscriber
     * @return void
     */
    public function removeSubscriber($event, callable $subscriber);
}
