<?php

namespace Eveniment;

class EventDispatcher implements EventDispatcherInterface
{
    protected $subscribers = [];

    public function on($event, callable $subscriber, $priority = 1000)
    {
        if (!isset($this->subscribers[$event])) {
            $this->subscribers[$event] = [];
        }

        if (!isset($this->subscribers[$event][$priority])) {
            $this->subscribers[$event][$priority] = [];
        }

        $this->subscribers[$event][$priority][] = $subscriber;
    }

    public function getSubscribers($event)
    {
        return isset($this->subscribers[$event]) ? $this->subscribers[$event] : [];
    }

    /**
     * Order the subscribers by priority
     * 
     * @param string $event
     */
    protected function orderSubscribersForEvent($event)
    {
        if (!isset($this->subscribers[$event])) {
            return;
        }

        krsort($this->subscribers[$event]);
    }

    public function dispatch($event, array $params = [])
    {
        $this->orderSubscribersForEvent($event);

        foreach ($this->getSubscribers($event) as $subscribersPriority) {
            foreach ($subscribersPriority as $subscriber) {
                call_user_func_array($subscriber, $params);
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

        foreach ($this->subscribers[$event] as $priority => &$subscribersPriority) {
            $index = array_search($subscriber, $subscribersPriority, true);

            if (false === $index) {
                continue;
            }

            unset($subscribersPriority[$index]);
            
            if (0 === count($subscribersPriority)) {
                unset($this->subscribers[$event][$priority]);
            }
        }
        
        return true;
    }
}
