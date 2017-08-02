<?php

use SilverStripe\ORM\DataObject;

class Car extends DataObject
{
    private static $db = array(
        'Colour' => 'Varchar(255)',
        'Model' => 'Varchar(255)',
        'Make' => 'Varchar(255)'
    );
}
