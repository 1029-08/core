<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * LiteCommerce
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@litecommerce.com so we can send you a copy immediately.
 * 
 * @category   LiteCommerce
 * @package    XLite
 * @subpackage Controller
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Controller\Admin;

/**
 * ____description____
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class ImportUsers extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Common method to determine current location
     *
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getLocation()
    {
        return 'Import user accounts';
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     * @access public
     * @since  3.0.0
     */
    public function getTitle()
    {
        return 'Import user accounts';
    }

    public $import_error = false;

    function init()
    {
        // FIXME - old code
        /* $p = new \XLite\Model\Profile();
        $this->import_fields = $p->get('importFields');*/
        $this->import_fields = array();

        parent::init();
    }
    
    function handleRequest()
    {
        if (substr($this->action, 0, 6) == "import" && !$this->checkUploadedFile()) {
        	$this->set('valid', false);
        	$this->set('invalid_file', true);
        }

        parent::handleRequest();
    }

    function action_import()
    {
        $this->startDump();
        $this->change_layout();
        $options = array(
            "file"              => $this->getUploadedFile(),
            "layout"            => $this->user_layout,
            "delimiter"         => $this->delimiter,
            "text_qualifier"    => $this->text_qualifier,
            "md5_import"        => $this->md5_import,
            "return_error"		=> true,
            );
        $p = new \XLite\Model\Profile();
        $p->import($options);
        $this->importError = $p->importError;
    }

    function change_layout($layout_name = "user_layout")
    {
        $layout = implode(',', (array) \XLite\Core\Request::getInstance()->$layout_name);
        \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption(
            array(
                'category' => 'ImportExport',
                'name'     => $layout_name,
                'value'    => $layout
            )
        );
    }
    function action_layout($layout_name = "user_layout")
    {
        $this->change_layout($layout_name);
    }

    function getPageReturnURL()
    {
        if ($this->action == "import") {
            $text = ($this->importError)?"Import process failed.":"Users are imported successfully.";
            return array($this->importError.'<br>'.$text.' <a href="admin.php?target=import_users"><u>Click here to return to admin interface</u></a>');
        } else {
            return parent::getPageReturnURL();
        }
    }
}
