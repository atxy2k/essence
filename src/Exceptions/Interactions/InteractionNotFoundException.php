<?php


namespace Atxy2k\Essence\Exceptions\Interactions;

use Exception;
class InteractionNotFoundException extends Exception
{

    /**
     * InteractionNotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Interaction not found'));
    }
}