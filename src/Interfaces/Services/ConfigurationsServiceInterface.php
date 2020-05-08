<?php


namespace Atxy2k\Essence\Interfaces\Services;


use Atxy2k\Essence\Eloquent\Configuration;

interface ConfigurationsServiceInterface
{
    function getConfiguration(string $key, $configurable = null) : ?Configuration;
    function setConfiguration(string $key, $value, $encode = false, $configurable = null) : ?Configuration;
    function removeConfiguration(string $key, $configurable = null) : bool ;
}