<?php

namespace SilverStripe\ElementLayoutAdmin\Forms;


// use SilverStripe\AssetAdmin\Controller\AssetAdmin;
// use SilverStripe\Assets\File;
// use SilverStripe\Assets\Folder;
use SilverStripe\Forms\FormField;
use SilverStripe\ORM\DataObject;

/**
 * For providing schema data to the client side to build a preview field with upload replacement feature
 */
class ElementLayoutField extends FormField
{
    /**
     * @var int
     */
    protected $recordID = null;

    protected $schemaDataType = FormField::SCHEMA_DATA_TYPE_CUSTOM;

    protected $schemaComponent = 'ElementLayoutField';

    public function getSchemaDataDefaults()
    {
        $defaults = parent::getSchemaDataDefaults();
        // $defaults['data']['uploadFileEndpoint'] = [
        //     'url' => AssetAdmin::singleton()->Link('api/uploadFile'),
        //     'method' => 'post',
        //     'payloadFormat' => 'urlencoded',
        // ];
        return $defaults;
    }

    public function getSchemaStateDefaults()
    {
        $defaults = parent::getSchemaStateDefaults();

        /** @var File $record */
        if ($record = $this->getRecord()) {
            $parent = $record->Parent();

            $defaults['data'] = array_merge_recursive($defaults['data'], [
                'id' => $record->ID,
                'parentid' => ($parent) ? (int) $parent->ID : 0,
                'url' => $record->Link(),
                'version' => (int) $record->Version,
                'exists' => $record->exists(),
                'nameField' => 'Name',
            ]);
        }
        return $defaults;
    }

    public function performReadonlyTransformation()
    {
        $this->setReadonly(true);

        return $this;
    }

    /**
     * @return DataObject
     */
    public function getRecord()
    {
        if ($this->recordID) {
            return DataObject::get_by_id(Car::class, $this->recordID);
        }
        return null;
    }

    /**
     * @param Integer $recordID
     * @return $this
     */
    public function setRecordID($recordID)
    {
        $this->recordID = $recordID;
        return $this;
    }
}
