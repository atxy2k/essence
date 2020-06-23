<?php


namespace Atxy2k\Essence\Traits;


use Atxy2k\Essence\Constants\NotificationType;

trait NotificationTrait
{

    public function convertNotification() : array
    {
        return [
            'type' => $this->getType(),
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'params' => $this->getParams(),
            'category' => $this->getCategory(),
            'icon' => $this->getIcon(),
        ];
    }

}