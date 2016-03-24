<?php

namespace Eveniment;

interface EventDispatcherInterface
{
    /**
     * @param string $event
     * @param callable $subscriber
     * @param integer $priority
     * @return void
     */
    public function on($event, callable $subscriber, $priority = 1000);

    /**
     * @param string $event
     * @return array
     */
    public function getSubscribers($event);

    /**
     * @param string $event
     * @param array $params
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
