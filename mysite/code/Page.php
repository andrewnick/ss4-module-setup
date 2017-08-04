<?php

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\TextField;
use SilverStripe\ElementLayoutAdmin\Forms\ElementLayoutField;

class Page extends SiteTree
{
    private static $db = array(
    	"Name" => 'Varchar(255)'
    );

    private static $has_one = array(
    );

    public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->addFieldToTab('Root.Main', ElementLayoutField::create('ElementLayout'), 'Content');

		return $fields;
	}
}
