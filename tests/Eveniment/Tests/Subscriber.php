<?php

namespace Eveniment\Tests;

class Subscriber
{
    public static function onStaticCall()
    {
        return 'hello';
    }

    public function onCall()
    {
        return 'hello world';
    }
}
