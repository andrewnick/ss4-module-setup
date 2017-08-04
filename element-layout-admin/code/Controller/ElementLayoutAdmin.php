<?php

namespace SilverStripe\AssetAdmin\Controller;

use InvalidArgumentException;
use SilverStripe\AssetAdmin\Forms\FolderCreateFormFactory;
use SilverStripe\AssetAdmin\Forms\FolderFormFactory;
use SilverStripe\Admin\LeftAndMainFormRequestHandler;
use SilverStripe\CampaignAdmin\AddToCampaignHandler;
use SilverStripe\Admin\CMSBatchActionHandler;
use SilverStripe\Admin\LeftAndMain;
use SilverStripe\AssetAdmin\BatchAction\DeleteAssets;
use SilverStripe\AssetAdmin\Forms\AssetFormFactory;
use SilverStripe\AssetAdmin\Forms\FileSearchFormFactory;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\AssetAdmin\Forms\FileFormFactory;
use SilverStripe\AssetAdmin\Forms\FileHistoryFormFactory;
use SilverStripe\AssetAdmin\Forms\ImageFormFactory;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\Image;
use SilverStripe\Assets\Storage\AssetNameGenerator;
use SilverStripe\Assets\Upload;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\RequestHandler;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormFactory;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\Member;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Security\Security;
use SilverStripe\Security\SecurityToken;
use SilverStripe\View\Requirements;
use SilverStripe\Versioned\Versioned;
use SilverStripe\ElementLayoutAdmin\Forms\ElementLayoutFormFactory;
use Exception;

/**
 * AssetAdmin is the 'file store' section of the CMS.
 * It provides an interface for manipulating the File and Folder objects in the system.
 * @skipUpgrade
 */
class ELementLayoutAdmin extends LeftAndMain
{
    private static $url_segment = 'element-layout';

    private static $url_rule = '/$Action/$ID';

    private static $menu_title = 'Element Layout';

    private static $menu_icon_class = 'font-icon-image';

    private static $tree_class = Folder::class;

    private static $url_handlers = [
        // Legacy redirect for SS3-style detail view
        // 'EditForm/field/File/item/$FileID/$Action' => 'legacyRedirectForEditView',
        // // Pass all URLs to the index, for React to unpack
        // 'show/$FolderID/edit/$FileID' => 'index',
        // // API access points with structured data
        // 'POST api/createFile' => 'apiCreateFile',
        // 'POST api/uploadFile' => 'apiUploadFile',
        // 'GET api/history' => 'apiHistory',
        // 'fileEditForm/$ID' => 'fileEditForm',
        // 'fileInsertForm/$ID' => 'fileInsertForm',
        // 'fileEditorLinkForm/$ID' => 'fileEditorLinkForm',
        // 'fileHistoryForm/$ID/$VersionID' => 'fileHistoryForm',
        // 'folderCreateForm/$ParentID' => 'folderCreateForm',
        // 'fileSelectForm/$ID' => 'fileSelectForm',
        'elementLayoutForm' => 'elementLayoutForm',
    ];

    /**
     * Amount of results showing on a single page.
     *
     * @config
     * @var int
     */
    private static $page_length = 50;

    /**
     * @config
     * @see Upload->allowedMaxFileSize
     * @var int
     */
    private static $allowed_max_file_size;

    /**
     * @config
     *
     * @var int
     */
    private static $max_history_entries = 100;

    /**
     * @var array
     */
    private static $allowed_actions = array(
        // 'legacyRedirectForEditView',
        // 'apiCreateFile',
        // 'apiUploadFile',
        // 'apiHistory',
        // 'folderCreateForm',
        // 'fileEditForm',
        // 'fileHistoryForm',
        // 'addToCampaignForm',
        // 'fileInsertForm',
        // 'fileEditorLinkForm',
        // 'schema',
        // 'fileSelectForm',
        'elementLayoutForm',
    );

    /**
     * Retina thumbnail image (native size: 176)
     *
     * @config
     * @var int
     */
    private static $thumbnail_width = 352;

    /**
     * Retina thumbnail height (native size: 132)
     *
     * @config
     * @var int
     */
    private static $thumbnail_height = 264;

    /**
     * Safely limit max inline thumbnail size to 200kb
     *
     * @config
     * @var int
     */
    private static $max_thumbnail_bytes = 200000;

    /**
     * Set up the controller
     */
    public function init()
    {
        parent::init();

        $module = ModuleLoader::getModule('element-layout-admin');
        Requirements::javascript($module->getRelativeResourcePath("client/dist/js/bundle.js"));
        Requirements::css($module->getRelativeResourcePath("client/dist/styles/bundle.css"));

    }

    public function getClientConfig()
    {
        $baseLink = $this->Link();
        return array_merge(parent::getClientConfig(), [
            'limit' => $this->config()->page_length,
            'form' => [
                // 'reactRouter' => true,
                'elementLayoutForm' => [
                    'schemaUrl' => $this->Link('schema/elementLayoutForm')
                ],
            ],
        ]);
    }

    /**
     * @todo Implement on client
     *
     * @param bool $unlinked
     * @return ArrayList
     */
    public function breadcrumbs($unlinked = false)
    {
        return null;
    }


    /**
     * Don't include class namespace in auto-generated CSS class
     */
    public function baseCSSClasses()
    {
        return 'ElementLayoutAdmin LeftAndMain';
    }

    /**
     * Build a form scaffolder for this model
     *
     * NOTE: Volatile api. May be moved to {@see LeftAndMain}
     *
     * @param File $file
     * @return FormFactory
     */
    // public function getFormFactory(File $file)
    // {
    //     // Get service name based on file class
    //     $name = null;
    //     if ($file instanceof Folder) {
    //         $name = FolderFormFactory::class;
    //     } elseif ($file instanceof Image) {
    //         $name = ImageFormFactory::class;
    //     } else {
    //         $name = FileFormFactory::class;
    //     }
    //     return Injector::inst()->get($name);
    // }

    /**
     * The form is used to generate a form schema,
     * as well as an intermediary object to process data through API endpoints.
     * Since it's used directly on API endpoints, it does not have any form actions.
     * It handles both {@link File} and {@link Folder} records.
     *
     * @param int $id
     * @return Form
     */
    public function getElementLayoutForm()
    {   
        $factory = ElementLayoutFormFactory::create();
        return $factory->getForm($this);
    }

    /**
     * Get file edit form
     *
     * @param HTTPRequest $request
     * @return Form
     */
    public function elementLayoutForm($request = null)
    {
        $factory = ElementLayoutFormFactory::create();
        return $factory->getForm($this);;
    }


    /**
     * Abstract method for generating a form for a file
     *
     * @param int $id Record ID
     * @param string $name Form name
     * @param array $context Form context
     * @return Form
     */
    // protected function getAbstractFileForm($id, $name, $context = [])
    // {
    //     /** @var File $file */
    //     $file = File::get()->byID($id);

    //     if (!$file) {
    //         $this->httpError(404);
    //         return null;
    //     }

    //     if (!$file->canView()) {
    //         $this->httpError(403, _t(
    //             'SilverStripe\\AssetAdmin\\Controller\\AssetAdmin.ErrorItemPermissionDenied',
    //             'You don\'t have the necessary permissions to modify {ObjectTitle}',
    //             '',
    //             ['ObjectTitle' => $file->i18n_singular_name()]
    //         ));
    //         return null;
    //     }

    //     // Pass to form factory
    //     $form = ElementLayoutFormFactory::getForm($this);

    //     // Set form action handler with ID included
    //     $form->setRequestHandler(
    //         LeftAndMainFormRequestHandler::create($form, [ $id ])
    //     );

    //     // Configure form to respond to validation errors with form schema
    //     // if requested via react.
    //     $form->setValidationResponseCallback(function (ValidationResult $error) use ($form, $id, $name) {
    //         $schemaId = Controller::join_links($this->Link('schema'), $name, $id);
    //         return $this->getSchemaResponse($schemaId, $form, $error);
    //     });

    //     return $form;
    // }

    /**
     * Gets a JSON schema representing the current edit form.
     *
     * WARNING: Experimental API.
     *
     * @param HTTPRequest $request
     * @return HTTPResponse
     */
    // public function schema($request)
    // {
    //     $formName = $request->param('FormName');

    //     // Get schema for history form
    //     // @todo Eventually all form scaffolding will be based on context rather than record ID
    //     // See https://github.com/silverstripe/silverstripe-framework/issues/6362
    //     $itemID = $request->param('ItemID');
    //     $version = $request->param('OtherItemID');
    //     $form = $this->getFileHistoryForm([
    //         'RecordID' => $itemID,
    //         'RecordVersion' => $version,
    //     ]);

    //     // Respond with this schema
    //     $response = $this->getResponse();
    //     $response->addHeader('Content-Type', 'application/json');
    //     $schemaID = $this->getRequest()->getURL();
    //     return $this->getSchemaResponse($schemaID, $form);
    // }


    /**
     * @param array $data
     * @param Form $form
     * @return HTTPResponse
     */
    public function save($data, $form)
    {
        return $this->saveOrPublish($data, $form, false);
    }

    /**
     * @param array $data
     * @param Form $form
     * @return HTTPResponse
     */
    public function publish($data, $form)
    {
        return $this->saveOrPublish($data, $form, true);
    }

    public function unpublish($data, $form)
    {
        if (!isset($data['ID']) || !is_numeric($data['ID'])) {
            return (new HTTPResponse(json_encode(['status' => 'error']), 400))
                ->addHeader('Content-Type', 'application/json');
        }

        $id = (int) $data['ID'];
        /** @var File $record */
        $record = DataObject::get_by_id(File::class, $id);

        if (!$record) {
            return (new HTTPResponse(json_encode(['status' => 'error']), 404))
                ->addHeader('Content-Type', 'application/json');
        }

        if (!$record->canUnpublish()) {
            return (new HTTPResponse(json_encode(['status' => 'error']), 401))
                ->addHeader('Content-Type', 'application/json');
        }

        $record->doUnpublish();
        return $this->getRecordUpdatedResponse($record, $form);
    }
}
