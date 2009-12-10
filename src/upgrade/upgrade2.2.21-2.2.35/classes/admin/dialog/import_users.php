<?php
/*
+------------------------------------------------------------------------------+
| LiteCommerce                                                                 |
| Copyright (c) 2003-2007 Creative Development <info@creativedevelopment.biz>  |
| All rights reserved.                                                         |
+------------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE  "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION.  THE AGREEMENT TEXT  IS ALSO AVAILABLE |
| AT THE FOLLOWING URLs:                                                       |
|                                                                              |
| FOR LITECOMMERCE                                                             |
| http://www.litecommerce.com/software_license_agreement.html                  |
|                                                                              |
| FOR LITECOMMERCE ASP EDITION                                                 |
| http://www.litecommerce.com/software_license_agreement_asp.html              |
|                                                                              |
| THIS  AGREEMENT EXPRESSES THE TERMS AND CONDITIONS ON WHICH YOU MAY USE THIS |
| SOFTWARE PROGRAM AND ASSOCIATED DOCUMENTATION THAT CREATIVE DEVELOPMENT, LLC |
| REGISTERED IN ULYANOVSK, RUSSIAN FEDERATION (hereinafter referred to as "THE |
| AUTHOR")  IS  FURNISHING  OR MAKING AVAILABLE TO  YOU  WITH  THIS  AGREEMENT |
| (COLLECTIVELY,  THE "SOFTWARE"). PLEASE REVIEW THE TERMS AND  CONDITIONS  OF |
| THIS LICENSE AGREEMENT CAREFULLY BEFORE INSTALLING OR USING THE SOFTWARE. BY |
| INSTALLING,  COPYING OR OTHERWISE USING THE SOFTWARE, YOU AND  YOUR  COMPANY |
| (COLLECTIVELY,  "YOU")  ARE ACCEPTING AND AGREEING  TO  THE  TERMS  OF  THIS |
| LICENSE AGREEMENT. IF YOU ARE NOT WILLING TO BE BOUND BY THIS AGREEMENT,  DO |
| NOT  INSTALL  OR USE THE SOFTWARE. VARIOUS COPYRIGHTS AND OTHER INTELLECTUAL |
| PROPERTY  RIGHTS PROTECT THE SOFTWARE. THIS AGREEMENT IS A LICENSE AGREEMENT |
| THAT  GIVES YOU LIMITED RIGHTS TO USE THE SOFTWARE AND NOT AN AGREEMENT  FOR |
| SALE  OR  FOR TRANSFER OF TITLE. THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY |
| GRANTED  BY  THIS AGREEMENT.                                                 |
|                                                                              |
| The Initial Developer of the Original Code is Creative Development LLC       |
| Portions created by Creative Development LLC are Copyright (C) 2003 Creative |
| Development LLC. All Rights Reserved.                                        |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */

/**
* Catalog import dialog
*
* @package Dialog
* @access public
* @version $Id: import_users.php,v 1.3 2007/05/21 11:53:27 osipov Exp $
*/
class Admin_Dialog_import_users extends Admin_Dialog
{
    var $import_error = false;

    function init()
    {
        $p = func_new("Profile");
        $this->import_fields = $p->get("importFields");
        parent::init();
    }
    
    function handleRequest()
    {
        if (substr($this->action, 0, 6) == "import" && !$this->checkUploadedFile()) {
        	$this->set("valid", false);
        	$this->set("invalid_file", true);
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
            "text_qualifier"    => $this->text_qualifier
            );
        $p = func_new("Profile");
        $p->import($options);
    }

    function change_layout($layout_name = "user_layout")
    {
        $layout = implode(',', $_POST[$layout_name]);
        $this->config =& func_new("Config");
        if ($this->config->find("name='$layout_name'")) {
            $this->config->set("value", $layout);
            $this->config->update();
        } else {
            $this->config->set("name", $layout_name);
            $this->config->set("category", "ImportExport");
            $this->config->set("value", $layout);
            $this->config->create();
        }
    }
    function action_layout($layout_name = "user_layout")
    {
        $this->change_layout($layout_name);
    }

	function getPageReturnUrl()
	{
		if ($this->action == "import") {
			return array('<br>Users are imported successfully. <a href="admin.php?target=import_users"><u>Click here to return to admin interface</u></a>');
		} else {
			return parent::getPageReturnUrl();
		}
	}
}

// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
