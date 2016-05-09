# Eveniment
**Eveniment** is a simple event dispatcher library for PHP. It's highly inspired by igorw/evenement library.   

[![Build Status](https://travis-ci.org/iosifch/eveniment.svg?branch=master)](https://travis-ci.org/iosifch/eveniment)
## Install
**Eveniment** can be installed via composer running the command below:
```
composer require eveniment/eveniment
```
Or fetching from the Github repository:
```
git clone git@github.com:iosifch/eveniment.git
```
## Test
Just run ``vendor/bin/phpunit`` and look at the green bar:)
## Usage
Firstly, create the dispatcher event:
```php
<?php

$dispatcher = new Eveniment\EventDispatcher();
```
Attach a simple subscriber to an event and after raise the event:
```php
<?php

$dispatcher->on('event.name', function($name) {
    echo $name;
});
$dispatcher->dispatch('event.name', ['Jon']);
```
You can set the subscriber priority also. However, the default priority is 1000:
```php
<?php

// This will be the last one called because the lower priority
$dispatcher->on('event.name', ['vendor\library\Class', 'onEventRaise'], 5);
// This subscriber will be the first one called because it has priority set to 1000
$dispatcher->on('event.name', function() {});
```
