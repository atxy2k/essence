<?php


namespace Atxy2k\Essence\Interfaces;


interface EssenceNotification
{
    function getType() : string;
    function getTitle() : string;
    function getMessage() : string;
    function getParams() : array;
    function getCategory() : int;
    function getIcon() : string;
}