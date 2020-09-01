<?php

use logger\SeasLogger;

class SeasLoggerTest extends PHPUnit\Framework\TestCase
{
    public $logData = [
        'test'
    ];

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $envPath = dirname(__FILE__).'/../.env';
        if( !is_file($envPath) ){
            exit('错误');
        }

        $envData = parse_ini_file($envPath);

        foreach ($envData as $key=>$value){
            putenv("{$key}=$value");
            $_ENV[$key]=$value;
            $_SERVER[$key]=$value;
        }

        parent::__construct($name, $data, $dataName);
    }

    public function testLogWrite()
    {
        $loggerService = new SeasLogger();
        $writeResult = $loggerService->log($this->logData,'log','test','test');
        $this->assertTrue($writeResult);
    }

    public function testDebugLogWrite()
    {
        $loggerService = new SeasLogger();
        $writeResult = $loggerService->debugLog($this->logData,'log','test','test');
        $this->assertTrue($writeResult);
    }
}