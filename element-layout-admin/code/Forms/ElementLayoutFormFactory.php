<?php

namespace SilverStripe\ElementLayoutAdmin\Forms;

use InvalidArgumentException;
use SilverStripe\Assets\File;
use SilverStripe\Control\RequestHandler;
use SilverStripe\Assets\Folder;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\FormFactory;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\PopoverField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextField;
use SilverStripe\ElementLayoutAdmin\Forms\ElementLayoutField;
use SilverStripe\Security\Group;
use SilverStripe\Forms\TreeDropdownField;

/**
 * @skipUpgrade
 */
class ElementLayoutFormFactory implements FormFactory
{
    use Extensible;
    use Injectable;
    use Configurable;

    // /**
    //  * Insert into HTML content area as a media object
    //  */
    // const TYPE_INSERT_MEDIA = 'insert-media';

    // /**
    //  * Insert into HTML content area as a link
    //  */
    // const TYPE_INSERT_LINK = 'insert-link';

    // /**
    //  * Select file by ID only
    //  */
    // const TYPE_SELECT = 'select';

    // /**
    //  * Edit form: Default
    //  */
    // const TYPE_ADMIN = 'admin';

    public function __construct()
    {
        // $this->constructExtensions();
    }

    /**
     * @param RequestHandler $controller
     * @param string $name
     * @param array $context
     * @return Form
     */
    public function getForm(RequestHandler $controller = null, $name = FormFactory::DEFAULT_NAME, $context = [])
    {
        // Validate context
        foreach ($this->getRequiredContext() as $required) {
            if (!isset($context[$required])) {
                throw new InvalidArgumentException("Missing required context $required");
            }
        }

        $fields = $this->getFormFields($controller, $name, $context);
        $actions = $this->getFormActions($controller, $name, $context);

        $form = Form::create($controller, $name, $fields, $actions);

        return $form;
    }

    /**
     * Get the validator for the form to be built
     *
     * @param RequestHandler $controller
     * @param $formName
     * @param $context
     * @return RequiredFields
     */
    protected function getValidator(RequestHandler $controller = null, $formName, $context = [])
    {
        $validator = new RequiredFields('Name');

        return $validator;
    }

    /**
     * @param RequestHandler $controller
     * @param $formName
     * @param array $context
     * @return FieldList
     */
    protected function getFormActions(RequestHandler $controller = null, $formName, $context = [])
    {
        $actions = new FieldList();

        return $actions;
    }

    /**
     * Get fields for this form
     *
     * @param RequestHandler $controller
     * @param string $formName
     * @param array $context
     * @return FieldList
     */
    protected function getFormFields(RequestHandler $controller = null, $formName, $context = [])
    {
        $fields = new FieldList(
            ElementLayoutField::create('ElementLayout')
        );

        return $fields;
    }

    public function getRequiredContext()
    {
        return [];
    }
}
