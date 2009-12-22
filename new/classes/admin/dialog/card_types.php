<?php
/*
+------------------------------------------------------------------------------+
| LiteCommerce                                                                 |
| Copyright (c) 2003-2009 Creative Development <info@creativedevelopment.biz>  |
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
* Class description.
*
* @package Dialog
* @access public
* @version $Id$
*
*/
class Admin_Dialog_card_types extends Admin_Dialog
{
	function obligatorySetStatus($status)
	{
		if (!in_array("status", $this->params)) {
			$this->params[] = "status";
		}
		$this->set("status", $status);
	}

    function action_delete()
    {
        if (isset($_POST["code"])) {
            $card = func_new("Card");
            if ($card->find("code='".$_POST["code"]."'")) {
                $card->delete();
            }
        }

		$this->obligatorySetStatus("deleted");
    }

    function action_add()
    {
		if ( empty($_POST["code"]) ) {
			$this->set("valid", false);
			$this->obligatorySetStatus("code");
			return;
		}

		if ( empty($_POST["card_type"]) ) {
			$this->set("valid", false);
			$this->obligatorySetStatus("card_type");
			return;
		}

        // checkboxes
        if (!isset($_POST["cvv2"])) {
            $_POST["cvv2"] = 0;
        }
        if (!isset($_POST["enabled"])) {
            $_POST["enabled"] = 0;
        }
        $card = func_new("Card");
        $card->set("properties", $_POST);
        if ($card->isExists()) {
            $this->set("valid", false);
            $this->obligatorySetStatus("exists");
            return;
        }
        
        $card->create();

		$this->obligatorySetStatus("added");
    }
    
    function action_update()
    {
        foreach ($_POST["card_types"] as $id => $data) {
            $data["enabled"] = array_key_exists("enabled", $data) ? 1 : 0;
            $data["cvv2"]    = array_key_exists("cvv2",    $data) ? 1 : 0;
            $card = func_new("Card"); 
            $card->set("properties", $data);
            $card->update();
        }

		$this->obligatorySetStatus("updated");
    }
}

// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
