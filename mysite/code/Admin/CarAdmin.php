<?php

use SilverStripe\Admin\ModelAdmin;

class CarAdmin extends ModelAdmin
{
	private static $url_segment = 'car-admin';

    private static $managed_models = array(
        Car::class
    );
}