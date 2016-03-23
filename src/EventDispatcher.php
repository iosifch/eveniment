<?php

namespace Eveniment;

class EventDispatcher implements EventDispatcherInterface
{
    protected $subscribers = [];

    public function on($event, callable $subscriber, $level = 1000)
    {
        if (!isset($this->subscribers[$event])) {
            $this->subscribers[$event] = [];
        }

        if (!isset($this->subscribers[$event][$level])) {
            $this->subscribers[$event][$level] = [];
        }

        $this->subscribers[$event][$level][] = $subscriber;
    }

    public function getSubscribers($event)
    {
        return isset($this->subscribers[$event]) ? $this->subscribers[$event] : [];
    }

    protected function orderSubscribersForEvent($event)
    {
        if (!isset($this->subscribers[$event])) {
            return;
        }

        ksort($this->subscribers[$event]);
    }

    public function dispatch($event, $params = null)
    {
        $this->orderSubscribersForEvent($event);

        foreach ($this->getSubscribers($event) as $subscribersLevel) {
            foreach ($subscribersLevel as $subscriber) {
                call_user_func($subscriber, $params);
            }
        }
    }

    public function removeSubscribers($event = null)
    {
        if (!is_null($event)) {
            unset($this->subscribers[$event]);
        } else {
            $this->subscribers = [];
        }
    }

    public function removeSubscriber($event, callable $subscriber)
    {
        if (!isset($this->subscribers[$event])) {
            return false;
        }

        foreach ($this->subscribers[$event] as &$subscribersLevel) {
            $index = array_search($subscriber, $subscribersLevel, true);

            if (false === $index) {
                return false;
            }

            unset($subscribersLevel[$index]);

            return true;
        }
    }
}
