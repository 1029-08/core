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
|                                                                              |
| The Initial Developer of the Original Code is Creative Development LLC       |
| Portions created by Creative Development LLC are Copyright (C) 2003 Creative |
| Development LLC. All Rights Reserved.                                        |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */

/**
* Class Inventory provides access to product inventory tracking card.
*
* @package Module_InventoryTracking
* @access public
* @version $Id: Inventory.php,v 1.38 2009/07/08 09:36:39 fundaev Exp $
*/
class Inventory extends Base
{
    /**
    * @var string $alias The credit cards database table alias.
    * @access public
    */
    var $alias = "inventories";

    var $primaryKey = array("inventory_id");
    var $defaultOrder = "inventory_id";

    /**
    * @var array $fields The inventory card properties.
    * @access private
    */
    var $fields = array(
            'inventory_id'    => '',  
            'inventory_sku'   => '',
            'amount'          => 0,
            'low_avail_limit' => 10,
            'enabled'         => 1,
            'order_by'        => 0,
        );

    var $importFields = array(
            "NULL" => false,
            "sku"  => false,
            "name" => false,
            "amount" => false,
            "low_avail_limit" => false,
            "enabled" => false,
            "order_by" => false,
            );

    function constructor($id = null) // {{{
    {
        parent::constructor($id);
		if ($this->xlite->get("ProductOptionsEnabled")) {
            $this->importFields["product_options"] = false;
            $this->importFields["inventory_sku"] = false;
        }
    } // }}}
    
    function _import(&$options) // {{{
    {
        $properties = $options["properties"];
        // search for the product first
        $product =& func_new("Product");
        $found = false;

        // search product by SKU
        if (!empty($properties["sku"]) && $product->find("sku='".addslashes($properties["sku"])."'")) {
            $found = true;
        }
        // .. or by NAME
        elseif (empty($properties["sku"]) && !empty($properties["name"]) && $product->find("name='".addslashes($properties["name"])."'")) {
            $found = true;
        }    

        static $line;
        if (!isset($line)) $line = 1; else $line++;
        echo "<b>Importing CSV file line# $line: </b>";

        if ($found) {
            // product found
            $inventory_id = $product->get("product_id") . (!empty($properties["product_options"]) ? "|".$properties["product_options"] : "");
            $inventory =& func_new("Inventory");
		    $inventory->set("properties", $properties);

            if ($inventory->find("inventory_id='$inventory_id'")) {
	            echo "updating amount for product " . $product->get("name") . "<br>\n";
    	        $inventory->update();
			} else {
				echo "creating amount for product " . $product->get("name") . "<br>\n";
				$inventory->set("inventory_id",!empty($properties['product_options']) ? $product->get("product_id")."|".$properties['product_options'] :  $product->get("product_id"));		
				$inventory->create();
			}
			$product->updateInventorySku();
        } else {
            echo "<font color=red>product not found:</font>".(!empty($properties["sku"]) ? " SKU: ".$properties["sku"] : "") . (!empty($properties["name"]) ? " NAME: ".$properties["name"] : "");
            echo '<br /><br /><a href="admin.php?target=update_inventory&page=amount"><u>Click here to return to admin interface</u></a>';
            die;
            
        }
    } // }}}

    function _export($layout, $delimiter) // {{{
    {
        $data = array();
        $inventory_id = $this->get("inventory_id");
        $pos = strpos($inventory_id, '|');
		if ($pos&&(!$this->xlite->get("ProductOptionsEnabled")||($this->xlite->get("ProductOptionsEnabled")&&!in_array("product_options",$layout))))
			return array();
        $product_id = $pos === false ? $inventory_id : substr($inventory_id, 0, $pos);
        $product =& func_new("Product", $product_id);
        if ($product->find("product_id='$product_id'")) {
            $values = $this->properties;
            foreach ($layout as $field) {
                if ($field == "NULL") {
                    $data[] = "";
                } elseif (isset($values[$field])) {
                    $data[] =  $this->_stripSpecials($values[$field]);
                } elseif ($field == "product_options") {
                    if ($pos) {
                        $data[] = $this->_stripSpecials(substr($inventory_id, $pos + 1));
                    } else {
                        $data[] = "";
                    }    
                } else {
                    $data[] = $this->_stripSpecials($product->get($field));
                }
            }
        }

        return $data;
    } // }}}
    
    function keyMatch($key) // {{{
    {
        // get the class:value pairs array
        $cardOptions = $this->parseOptions($this->get("inventory_id"));
        $keyOptions = $this->parseOptions($key);
        $intersect = array_intersect($cardOptions, $keyOptions);
        $diff = array_diff($cardOptions, $intersect);
        return empty($diff);
    } // }}}

    function parseOptions($id) // {{{
    {
        $options = array();
        if (strpos($id, "|") !== false) {
            $options = explode("|", $id);
            if (isset($options[0])) {
            	unset($options[0]);
            }
        }
        return $options;
    } // }}}

    function checkLowLimit(&$item) // {{{
    {
        if ($this->get("amount") < $this->get("low_avail_limit")) {
            $inventory_id = $this->get("inventory_id");
            $pos = strpos($inventory_id, '|');
            $product_id = $pos === false ? $inventory_id : substr($inventory_id, 0, $pos);

            // send low limit notification
            $mailer = func_new("Mailer");
            $mailer->set("product", func_new("Product",$product_id));
            $mailer->set("item", $item);
            $mailer->set("amount", $this->get("amount"));
            $mailer->compose(
                    $this->config->get("Company.site_administrator"),
                    $this->config->get("Company.site_administrator"),
                    "lowlimit_warning_notification");
            $mailer->send();
        }
    } // }}}

    function &get($property)
    {
    	switch($property) {
    		case "amount":
    			return $this->getAmount();
    		default:
    			return parent::get($property);
    	}
    }

    function getAmount()
    {
    	$amount = parent::get("amount");
		if (!$this->xlite->is("adminZone")) {
        	return ($amount < 0) ? 0 : $amount;
        } else {
        	return $amount;
        }
    }
}

// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
