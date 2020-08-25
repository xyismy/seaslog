<?php

class SeasLogException extends Exception
{
    public function __construct($msg,$code='-1')
    {
        throw new Exception($msg,$code);
    }

}