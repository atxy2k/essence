<?php

namespace Atxy2k\Essence\Interfaces;

use Illuminate\Support\MessageBag;

interface ServiceInterface
{
    public function errors() : MessageBag;
    public function pushErrors(MessageBag $errors) : ServiceInterface;
    public function pushError(string $message, string $key = 'error') : ServiceInterface;
    public function cleanErrors() : ServiceInterface;
}