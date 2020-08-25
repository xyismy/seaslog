<?php

require __DIR__.'/vendor/autoload.php';


$log = new logger\SeasLogger();
var_dump($log);

//var_dump(class_exists('logger'));
//var_dump(class_exists('seaslog'));
//var_dump(get_class('seaslog'));


//var_dump(\seaslog\src\SeasLogger::class);
//$class = new ReflectionClass('seaslog');
//var_dump($class->getName());
//var_dump($class->getProperties());

//var_dump(getenv());