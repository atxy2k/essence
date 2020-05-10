<?php


namespace Atxy2k\Essence\Exceptions\Interactions;

use Exception;
class InteractionNotCreatedException extends Exception
{

    /**
     * InteractionNotCreatedException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Interaction not created exception'));
    }
}