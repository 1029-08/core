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
* Class represents the product extra-field.
*
* @package kernel
* @access public
* @version $Id: ExtraField.php,v 1.3 2007/05/21 11:53:28 osipov Exp $
*/
class ExtraField extends Base
{
    var $fields = array(
            "field_id" => 0,    // primary key
            "product_id" => 0,
            "name"     => "",
            "default_value"    => "",
            "enabled"  => 1,
            "order_by" => 0,
			"parent_field_id" => 0,
			"categories" => "",
            );

    var $autoIncrement = "field_id";
    var $alias         = "extra_fields";
    var $defaultOrder  = "order_by,name";

    var $importFields = array(
        "NULL" => false,
        "name" => false,
        "sku" => false,
        "category" => false,
        "product" => false,
        "value"    => false,
        "default_value" => false,
        "enabled" => false,
        "order_by" => false
    );
    
    function &getProduct() // {{{
    {
        return func_new("Product", $this->get("product_id"));
    } // }}}
    
    function delete()
    {
        if ($this->get("parent_field_id") == 0)
        {
    		$ef_children =& $this->getChildren();
    		if (is_array($ef_children)) {
    			foreach($ef_children as $ef_child) {
    				$ef_child->delete();
    			}
    		}
    	}

        // delete all values
        $fv =& func_new("FieldValue");
        $valuesTable = $this->db->getTableByAlias($fv->alias);
        $sql = "DELETE FROM $valuesTable WHERE field_id=" . $this->get("field_id");
        $this->db->query($sql);
        // delete field
        parent::delete();
    }

    function update()
    {
        if ($this->get("parent_field_id") == 0) {
    		$ef_children =& $this->getChildren();
    		if (is_array($ef_children)) {
    			foreach($ef_children as $ef_child) {
    				if ($ef_child->get("name") == $this->get("name")) {
    					$ef_child->set("enabled", $this->get("enabled"));
    					$ef_child->update();
    				}
    			}
    		}

    		if (is_array($this->get("categories"))) {
            	$this->setCategoriesList($this->get("categories"));
            }

			if ($this->get("product_id") == 0) {
				$ef_children =& func_new("ExtraField");
				$ef_children = $ef_children->findAll("parent_field_id='".$this->get("field_id")."'");
				if (is_array($ef_children) && count($ef_children) > 0) {
					$categories_list = explode("|", $this->get("categories"));
					foreach ($ef_children as $ef_child) {
						$ef_child->setCategoriesList($categories_list);
						$ef_child->update();
					}
				}
			}

			$categories = $this->get("categories");
    		$categories_old = $this->get("categories_old");
            if (isset($categories) && isset($categories_old) && strcmp($categories, $categories_old) != 0) {
    			$categories = explode("|", $categories);
    			$categories_old = explode("|", $categories_old);
    			if (is_array($categories_old) && is_array($categories)) {
    				foreach($categories_old as $cat_old) {
    					if (!in_array($cat_old, $categories)) {
    						$product =& func_new("Product");
                            $products =& $product->advancedSearch("", null, $cat_old);
                            if (is_array($products) && count($products) > 0) {
                            	foreach($products as $product) {
                            		$ef_children =& func_new("ExtraField");
                                    $ef_children->set("ignoreFilter", true);
    								$ef_children = $ef_children->findAll("parent_field_id='".$this->get("field_id")."' AND product_id='".$product->get("product_id")."'");
    								if (is_array($ef_children) && count($ef_children) > 0) {
    									foreach($ef_children as $ef_child) {
    										$ef_child->delete();
    									}
    								}
                            	}
                            }
    					}
    				}
    			}
            }
    	}

		parent::update();
    }

    function filter()
    {
    	if ($this->get("ignoreFilter"))
    		return true;

        if (!$this->xlite->is("adminZone")) {
            return (boolean) $this->get("enabled");
        }
        return parent::filter();
    }

    function &getImportFields()
    {
        return parent::getImportFields("fields_layout");
    }
    
    function _export($layout, $delimiter) // {{{
    {
        $data = array();
        // export field descriptions
        foreach ($layout as $field) {
            switch ($field) {
                case "NULL":
                    $data[] = "";
                    break;
                case "sku":
                    $data[] = $this->get("product.sku");
                    break;
                case "category":
					$properties = $this->get("properties");
					if($properties['product_id']==0) {
						if (!empty($properties['categories'])) 
						{
							$categories = array();
							$categories = explode('|',$properties['categories']);
							foreach($categories as $category) 
							{
								$cat =& func_new('Category',$category);
								$cat_path .= '|'.$cat->getStringPath();
							}
							$data[] = substr($cat_path, 1);
						}
						else $data[] = "";	
					}
					else { 
	                    $product =& $this->get("product");
    	                $data[] = $product->_exportCategory();
					} 
                    break;
                case "product":
                    $data[] = $this->get("product.name");
                    break;
                default:    
                    $data[] = $this->get($field);
                    break;
            }
        }
        return $data;
    } // }}}

    function _import(&$options) // {{{
    {
        $properties = $options["properties"];
		//import global extra fields 
	
		if ($properties['sku']==NULL && $properties['product']==NULL)	
		{
			$global_extra_field =& func_new ("ExtraField");
			$found = $global_extra_field->find("name='".addslashes($properties['name'])."' AND product_id=0");
			$global_extra_field->set("default_value", $properties["default_value"]);
			$global_extra_field->set("name", $properties["name"]);
			$global_extra_field->set("enabled", $properties["enabled"]);
			$global_extra_field->set("order_by", $properties["order_by"]);	
			$global_extra_field->set("product_id", 0);
			$global_extra_field->set("parent_field_id", 0);
			
			if ($properties["category"] == NULL)
				$global_extra_field->set("categories",""); 
			else 
			{
				$category =& func_new("Category");
				foreach($category->parseCategoryField($properties["category"],true) as $path)	
				{
					$cat = $category->findCategory($path);
					if(!is_null($cat)) $categories .= "|".$cat->get("category_id");
				}
				$categories = substr($categories,1);
				$global_extra_field->set("categories", $categories);
			}
			
			if(!$found)	{
				$global_extra_field->create();
			} else {
				$global_extra_field->update();
			}
	
			$extra_fields  =& func_new("ExtraField");
			$extra_fields_ = $extra_fields->findAll("name='".addslashes($properties["name"])."' AND product_id<>0");
			if (!empty($extra_fields_))
				foreach($extra_fields_ as $extra_field_) 
				{
					$extra_field_->set("parent_field_id", $global_extra_field->get("field_id"));
					$extra_field_->update();
				}
					
		} else {
			$product =& func_new("Product");
        	$product =& $product->findImportedProduct($properties["sku"], $properties["category"], $properties["product"], false /* do not create categories */);
			if (is_null($product)) {
				echo "<font color=red>Product not found for line ".$this->lineNo."</font><br>";
			}	
			else {
				$productID = $product->get("product_id");
				$field =& func_new("ExtraField");
				$global_extra_field =& func_new("ExtraField");
				$global_found = $global_extra_field->find("name='".addslashes($properties["name"])."' AND product_id=0");
				if ($global_found)
					$field->set("parent_field_id", $global_extra_field->get("field_id"));
				else 
					$field->set("parent_field_id", 0);

    		    $found = $field->find("name='".addslashes($properties["name"])."' AND product_id=$productID");
		        if (!$found) {
        		    echo "&gt; Field \"".$properties["name"]."\" created for product # $productID<br>\n";
		            $field->create();
		        } else {
		            echo "&gt; Field \"".$properties["name"]."\" updated for product # $productID<br>\n";
		        }

		        $field->set("default_value", $properties["default_value"]);
				$field->set("value", $properties["value"]);
		        $field->set("enabled", $properties["enabled"]);
				$field->set("order_by", $properties["order_by"]);
		        $field->set("name", $properties["name"]);
		        $field->set("product_id", $productID);
		        $field->update();

				$fieldID = $field->get("field_id");
				// search and update or create field value
        		if (!empty($properties["value"]) && strlen($properties["value"])) {
            		$fv =& func_new("FieldValue");
            		$found = $fv->find("field_id=$fieldID AND product_id=$productID");
            		$fv->set("field_id", $fieldID);
            		$fv->set("product_id", $productID);
            		if (!$found) {
                		$fv->create();
            		}
            		$fv->set("value", $properties["value"]);
                	$fv->update();
				}
			}	
		}	
    } // }}}
	
	function isGlobal()
	{
			$categories = $this->getCategories();
			return empty($categories);
	}
	
	function getCategories()
	{
		if (!isset($this->categories))
		{
    		$categories = explode("|", $this->get("categories"));
    		if (!is_array($categories))
    		{
    			$categories = array();
    		}
    		if (count($categories) == 1 && empty($categories[0]))
    		{
    			$categories = array();
    		}

            $this->categories = $categories;
		}
		return $this->categories;
	}

	function setCategoriesList($categories)
	{
		if (is_array($categories))
		{
			$categories = array_values($categories);
            $categories = implode("|", $categories);
		}
		else
		{
			$categories = "";
		}

		$this->set("categories", $categories);
	}

    function isCategorySelected($categoryID)
    {
    	$this->getCategories();

    	if (count($this->categories) == 0)
    		return true;

        return in_array($categoryID, $this->categories);
    }

    function &getChildren()
    {
        if ($this->get("parent_field_id") == 0)
        {
    		$ef_children =& func_new("ExtraField");
            $ef_children->set("ignoreFilter", true);
    		$ef_children = $ef_children->findAll("parent_field_id='".$this->get("field_id")."'");
    		if (!(is_array($ef_children) && count($ef_children) > 0))
    		{
    			$ef_children = array();
    		}
    	}
    	else
    	{
    		$ef_children = array();
    	}

    	return $ef_children;
    }

    /**
    * Removes all records that have no corresponding link to product(s).
    */
    function collectGarbage()
    {
        $products_table = $this->db->getTableByAlias("products");
        $extra_fields_table = $this->db->getTableByAlias("extra_fields");
        $sql = "SELECT e.field_id, e.product_id " . 
               "FROM $extra_fields_table e " . 
               "LEFT OUTER JOIN $products_table p " . 
               "ON e.product_id=p.product_id " . 
               "WHERE p.product_id IS NULL AND e.product_id<>0";
        $result = $this->db->getAll($sql);
        foreach ($result as $info) {
            $ef =& func_new("ExtraField", $info["field_id"]);
            $ef->_collectGarbage();
        }
    } // }}}

    function _collectGarbage() // {{{
    {
        $this->delete();
    } // }}}
} 

// WARNING:
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
