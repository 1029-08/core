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
class XLite_Controller_Admin_Category extends XLite_Controller_Admin_Abstract
{	
    public $page = "category_modify";	
    public $pages = array
    (
    	"category_modify"		=> "Add/Modify category",
    );	
    public $pageTemplates = array
    (
    	"category_modify"  		=> "categories/add_modify_body.tpl",
    	"extra_fields"  		=> "categories/category_extra_fields.tpl",
    );	

    public $params = array('target', 'category_id', 'mode', 'message', 'page');	
    public $order_by = 0;
    
    function init()
    {
    	parent::init();

    	if ($this->mode != "add" && $this->mode == "modify") {
			$this->pages["category_modify"] = "Modify category";
			if ($this->config->get("General.enable_categories_extra_fields")) {
				$this->pages["extra_fields"] = "Extra fields";
			}
		} else {
			$this->pages["category_modify"] = "Add new category";
		}
    }

    function fillForm()
    {
        $this->set("properties", $this->get("category.properties"));
    }

    function getCategories()
    {
        $c = new XLite_Model_Category();
        $this->categories = $c->findAll();
        $names = array();
        $names_hash = array();
        for ($i = 0; $i < count($this->categories); $i++) 
        {
            $name = $this->categories[$i]->get("stringPath");
            while (isset($names_hash[$name]))
            {
            	$name .= " ";
            }
            $names_hash[$name] = true;
            $names[] = $name;
        }
        array_multisort($names, $this->categories);

        return $this->categories;
    }

    function getExtraFields()
    {
        if (is_null($this->extraFields)) 
        {
            $ef = new XLite_Model_ExtraField();
            $extraFields = $ef->findAll("product_id=0");  // global fields
            foreach($extraFields as $extraField_key => $extraField)
            {
            	if (!$extraField->isCategorySelected($this->category_id))
            	{
            		unset($extraFields[$extraField_key]);
            	}
            }

            $this->extraFields = (count($extraFields) > 0) ? $extraFields : null;
        }
        return $this->extraFields;
    }

    function getParentCategory()
    {
        if (is_null($this->parentCategory)) {
            $this->parentCategory = new XLite_Model_Category($this->category_id);
        }
        return $this->parentCategory;
    }
    
    function getCategory()
    {
        if (is_null($this->category)) {
            if ($this->get("mode") == "add") {
                $this->category = new XLite_Model_Category(); // empty category
            } else {
                $categoryID = 0;
                if (isset($_REQUEST["category_id"])) {
                    $categoryID = $_REQUEST["category_id"];
                }
                $this->category = new XLite_Model_Category($categoryID);
            }
        }
        return $this->category;
    }

    function getLocationPath()
    {
        $result = array();
        if ($this->get("mode") == "add" && $this->get("parentCategory.category_id") != 0) {
            foreach ($this->get("parentCategory.path") as $category) {
				$name = $category->get("name");
				while (isset($result[$name])) {
					$name .= " ";
				}
				$result[$name] = "admin.php?target=categories&category_id=" . $category->get("category_id");
            }
        } else if ($this->get("category.category_id") != 0) {
            foreach ($this->get("category.path") as $category) {
				$name = $category->get("name");
				while (isset($result[$name])) {
					$name .= " ";
				}
				$result[$name] = "admin.php?target=categories&category_id=" . $category->get("category_id");
            }
        }
        return $result;
    }

    function action_modify()
    {
		$this->stripHTMLtags($_POST, array("name"));
        $valid = (bool) isset($this->name) && strlen(trim($this->name));

        if (!$valid) {
            $this->set("valid", $valid);
            return;
        }
        // update category
        $category = new XLite_Model_Category();
		if (empty($_POST['parent'])) $_POST['parent'] = 0;
        $category->set("properties", $_POST);
        $category->update();

        // update category image
        $image = $category->get("image");
        $image->handleRequest();
        
        $this->set("message", "updated");
    }

    function action_add()
    {
		$this->stripHTMLtags($_POST, array("name"));
        $valid = (bool) isset($this->name) && strlen(trim($this->name));

        if (!$valid) {
            $this->set("valid", $valid);
            return;
        }
        // add category
        $category = new XLite_Model_Category();
        $category->set("properties", $_POST);
        $category->set("category_id", null);
        if (empty($_POST['parent'])) $_POST['parent'] = 0;
        $category->set("parent",$_POST['parent']);
        $category->create();

        // upload category image
        $image = $category->get("image");
        $image->handleRequest();

        // switch to modify page
        $this->set("category_id", $category->get("category_id"));
        $this->set("mode", "modify");
        $this->set("message", "added");
    }

    function action_delete()
    {
        $category = $this->get("category");
        // return to categories listing
        $this->set("target", "categories");
        $this->set("category_id", $category->get("parent"));
        $category->delete();
    }

    function action_icon()
    {
        $category = $this->get("category");
        // delete category image
        $image = $category->get("image");
        $image->handleRequest();
    }

    function action_add_field()
    {
    	foreach($_POST as $post_key => $post_value)
    	{
    		if (strcmp(substr($post_key, 0, 7), "add_ef_") == 0)
    		{
    			$_POST[substr($post_key, 7)] = $post_value;
    			unset($_POST[$post_key]);
    		}
    	}
        // ADD field
        if (!is_null($this->get("add_field"))) 
        {
            $categories = (array)$this->get("add_categories");
            if (!empty($categories)) 
            {
                $ef = new XLite_Model_ExtraField();
                $ef->set("properties", $_POST);
                $ef->setCategoriesList($categories);
                $ef->create();
            }
            else
            {
                // buld add
                $categories = (array)$this->get("add_categories");
                if (!empty($categories)) {
                    foreach ($categories as $categoryID) {
                        $category = new XLite_Model_Category($categoryID);
                        foreach ((array)$category->get("products") as $product) {
                            $ef = new XLite_Model_ExtraField();
                            $ef->set("properties", $_POST);
                            $ef->set("product_id", $product->get("product_id"));
                            $ef->create();
                        }
                    }
                } else {    
                    $ef = new XLite_Model_ExtraField();
                    $ef->set("properties", $_POST);
                    $ef->create();
                }    
            }
        }
        // DELETE field
        elseif (!is_null($this->get("delete_field"))) {
            foreach ((array)$this->get("add_categories") as $categoryID) {
                $category = new XLite_Model_Category($categoryID);
                foreach ((array)$category->get("products") as $product) {
                    $ef = new XLite_Model_ExtraField();
                    if ($ef->find("name='".addslashes($this->get("name"))."' AND product_id=".$product->get("product_id"))) {
                        $ef->delete();
                    }    
                }
            }
        }
    }

    function action_update_fields()
    {
        if (!is_null($this->get("delete")) && !is_null($this->get("delete_fields"))) 
        {
			$category_id = $this->get("category_id");
            foreach ((array)$this->get("delete_fields") as $id) {
				$data = array();
                $ef = new XLite_Model_ExtraField($id);
				$categories = $ef->getCategories();
				if ( !is_array($categories) || count($categories) == 0 ) {
					$cat = new XLite_Model_Category();
					$cats = $cat->findAll();
					$categories = array();
					foreach ($cats as $v)
						$categories[] = $v->get("category_id");
				}

				$data = array_diff($categories, array($category_id));
				if ( !is_array($data) || count($data) == 0 ) {
					$ef->delete();
					return;
				}

				$ef->set("categories", $data);
				$ef->update();
            }
        } 
        elseif (!is_null($this->get("update"))) 
        {
            foreach ((array)$this->get("extra_fields") as $id => $data) 
            {
                $ef = new XLite_Model_ExtraField($id);
                $ef->set("categories_old", $ef->get("categories"));
                $ef->set("properties", $data);
                $ef->update();
            }
        }
    }
}

// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
