# Eveniment
**Eveniment** is a simple event dispatcher library for PHP. It's highly inspired by igorw/evenement library.   

[![Build Status](https://travis-ci.org/iosifch/eveniment.svg?branch=master)](https://travis-ci.org/iosifch/eveniment)
## Install
**Eveniment** can be installed via composer running the command below:
```
composer require eveniment/eveniment=dev-master
```
Or fetching from the Github repository:
```
git clone git@github.com:iosifch/eveniment.git
```
## Usage
Firstly, create the dispatcher event:
```php
<?php

$dispatcher = new Eveniment\EventDispatcher();
```
Attach a simple subscriber to an event:
```php
<?php

$dispatcher->on('event.name', function($name) {
    echo $name;
});
$dispatcher->dispatch('event.name', 'Jon');
```
You can set the subscriber priority also. However, the default priority is 1000:
```php
<?php

// This subscriber will be the last one called because it has priority set to 1000
$dispatcher->on('event.name', function() {});
// This will be the first one called
$dispatcher->on('event.name', ['vendor\library\Class', 'onEventRaise'], 5);
```




