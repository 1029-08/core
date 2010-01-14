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
* Tax management dialog.
*
* @package Dialog
* @access public
* @version $Id$
*
*/
class XLite_Controller_Admin_Taxes extends XLite_Controller_Admin_Abstract
{	
    public $params = array('target', 'page', 'mode', 'ind');	
    public $page = "options";	
    public $pages = array('options' => 'Tax Options',
                       'rates' => 'Rates/Conditions',
                       'schemes' => 'Tax Scheme Definitions');	
					   
	public $pageTemplates = array( "options" => "tax/options.tpl", 
								"rates" => "tax/rates.tpl",	
								"schemes"	=> "tax/schemas.tpl",
								"add_rate"	=> "tax/add.tpl");	 
	public $taxes;	
    public $_rates;	
    public $_levels;	
    public $invalidExpressions = array();
    
    function init()
    {
        parent::init();
		if ($this->get("page") == "add_rate") {
			$this->pages = array("add_rate" => ($this->get("mode") == "edit" ? "Edit" : "Add")." rate/condition");
			$this->pageTemplates = array("add_rate" => "tax/add.tpl");
		}

        $this->taxes = new XLite_Model_TaxRates();
        $this->getRates();

        if ($this->get("mode") == "add") {
            $this->initRuleParams();
        } elseif ($this->get("mode") == "edit") {
            $this->action_edit();
        }

        if ($this->page == "rates" && !isset($this->action)) {
            foreach ($this->_rates as $ind_rate => $rate) {
            	$value = $this->getCurrentVarValue($ind_rate, $rate);
            	if (!isset($value)) {
            		$value = $this->getCurrentCondVarValue($ind_rate, $rate);
            	}
            	if (!isset($value)) {
            		continue;
            	}
    			$value = trim($value);
    			$this->_validateValue($value, $ind_rate);
            }
            if (count($this->invalidExpressions) > 0) {
                $this->set('inv_exp_error', 1);
                $this->valid = false;
            }
        }
    }
   	
   	function action_add_tax()	
	{
        $taxes = unserialize($this->config->get("Taxes.taxes"));
        if ($_POST["new_name"] != "") {
			if (empty($_POST['new_pos'])) {
				$_POST["new_pos"] = ((int)(max(is_array($_POST["pos"]) ? $_POST["pos"] : array("1"))/10)+1)*10;
			}
																	
			$taxes[] = array("pos" => $_POST["new_pos"], "name" => $_POST["new_name"], "display_label" => $_POST["new_display_label"], "registration" => $_POST["new_registration"]);
            if (!isset($_POST["pos"])) {
                $_POST["pos"] = array();
            }
            $_POST["pos"][] = $_POST["new_pos"];
        }
        if (isset($_POST["pos"])) {
            array_multisort($_POST["pos"], $taxes);
        }
        $schema = array("taxes" => $taxes, "use_billing_info" => $_POST["use_billing_info"], "prices_include_tax" => $_POST["prices_include_tax"],"include_tax_message" => $_POST["include_tax_message"]);
        $this->taxes->setSchema($schema);
	}
	
    function action_update_options()
    {
        $taxes = array();
        if (isset($_POST["pos"])) {
            foreach ($_POST["pos"] as $ind => $pos) {
				if (empty($pos)) {
					$_POST["pos"][$ind] = ((int)(max($_POST["pos"])/10)+1)*10;
				}	
				$tax = array("pos" => $_POST["pos"][$ind], "name" => $_POST["name"][$ind], "display_label" => $_POST["display_label"][$ind], "registration" => $_POST["registration"][$ind]);
                $taxes[] = $tax;
            }
			array_multisort($_POST["pos"],$taxes);
        }

        $schema = array("taxes" => $taxes, "use_billing_info" => $_POST["use_billing_info"], "prices_include_tax" => $_POST["prices_include_tax"],"include_tax_message" => $_POST["include_tax_message"]);
        $this->taxes->setSchema($schema);
    }

    function action_delete_tax()
    {
		if ($this->get("deleted")) {
	        $taxes = unserialize($this->config->get("Taxes.taxes"));
			$deleted = $this->get("deleted");
			foreach($deleted as $key => $value) {
				unset($taxes[$key]);
			}
    	    $c = new XLite_Model_Config();
	        $c->set("category", "Taxes");
	        $c->set("name", "taxes");
    	    $c->set("value", serialize($taxes)); 
        	$c->update();
		}	
    }

    function action_reset()
    {
        // reset to a pre-defined schema
        $schemaName = $_POST["schema"];
        $this->taxes->setPredefinedSchema($schemaName);
        $this->page = "options";
    }

    function action_delete_schema()
    {
        $name = $_POST["schema"];
        $tax = new XLite_Model_TaxRates();
        $tax->saveSchema($name, null);
    }
    
    function _validateValue($value, $ind_rate)
    {
    	static $tax;

    	if (!isset($tax)) {
        	$tax = new XLite_Model_TaxRates();
    	}

		// find the corresponding cell in the rates tree
		$ptr = $this->locateNode($this->taxes->_rates, $this->_levels[$ind_rate]);
		$tax_name = $this->getNoteTaxName($ptr);

        // check expression {{{
		if (($value{0} != '=') && !preg_match("/^\d+(\.\d+)?$/", $value)) {
			$this->error = "Tax value must be a number or contain '=' at its start: '$value'";
			unset($this->invalidExpressions[$ind_rate]);
			$this->invalidFormula[$ind_rate] = $value;
			$this->set("valid", false);
            return;
		} else {
			unset($this->invalidFormula[$ind_rate]);
		}

        if (!preg_match("/^\d+(\.\d+)?$/", $value) && !$tax->checkExpressionSyntax($value, $this->invalidExpressions[$ind_rate], $tax_name)) {
            $this->invalidExpressions[$ind_rate] = '"<b>'.join('</b>", "<b>', $this->invalidExpressions[$ind_rate]).'</b>"';
            return;
        } else {
            unset($this->invalidExpressions[$ind_rate]);
        }
        // }}}

        if (is_array($ptr)) {
            // conditional
            $ptr["action"] = $this->_insertValue($ptr["action"], $value);
        } else {
            $ptr = $this->_insertValue($ptr, $value);
        }
    }

    function action_update_rates()
    {
        // update rates
        if (isset($_POST["varvalue"])) {
            foreach ($_POST["varvalue"] as $ind => $value) {
                $value = trim($value);
				$this->_validateValue($value, $ind);
            }
            if (count($this->invalidExpressions) > 0) {
                $this->set('inv_exp_error', 1);
                $this->valid = false;
            }
        }
        // sort rates
        if (isset($_POST["pos"])) {
            // build a pos tree
            $posTree = array();
            foreach ($_POST["pos"] as $ind => $pos) {
                $levels = $this->_levels[$ind];
                array_pop($levels);
                // locate the corresponding pos array in the pos tree
                $ptr = $this->locateNode($posTree, $levels);
                if (!isset($ptr["orderbys"])) {
                    $ptr["orderbys"] = array();
                }
                $ptr["orderbys"][$ind] = $pos;
            }
            // sort all lists recursively
            $this->_sortRates($this->taxes->_rates, $posTree);
        }
        // store
        $this->taxes->setSchema(array("tax_rates" => $this->taxes->_rates));
    }

    function action_open()
    {
        $ind = $_REQUEST["ind"];
        $node = $this->locateNode($this->taxes->_rates, $this->_levels[$ind]);
        $node["open"] = true;
        // store
        $this->taxes->setSchema(array("tax_rates" => $this->taxes->_rates));
        $this->getRates(); // re-build the tree
    }

    function action_all()
    {
        $this->changeAll($this->taxes->_rates, $_REQUEST["open"]);
        // store
        $this->taxes->setSchema(array("tax_rates" => $this->taxes->_rates));
        $this->getRates(); // re-build the tree
    }
    
    function changeAll(&$tree, $open)
    {
        for ($i=0; $i<count($tree); $i++) {
            if ($this->isCondition($tree[$i])) {
                if ($open) {
                    $tree[$i]["open"] = true;
                } else {
                    if (isset($tree[$i]["open"])) {
                        unset($tree[$i]["open"]);
                    }
                }
                $this->changeAll($tree[$i]["action"], $open);
            }
        }
    }
   
    function action_close()
    {
        $ind = $_REQUEST["ind"];
        $node = $this->locateNode($this->taxes->_rates, $this->_levels[$ind]);
        if (isset($node["open"])) {
            unset($node["open"]);
        }    
        // store
        $this->taxes->setSchema(array("tax_rates" => $this->taxes->_rates));
        $this->getRates(); // re-build the tree
    }

    function action_edit()
    {
        $this->taxes = new XLite_Model_TaxRates();
        if (isset($_REQUEST["ind"]) && $_REQUEST["ind"] !== '') {
            $this->ind = $ind = $_REQUEST["ind"];
            $this->tax = $this->locateNode($this->taxes->_rates, explode(',',$ind));
        } else {
            $this->tax = '';
        }
        $this->initRuleParams();
        $this->edit = 1;
    }
   
    function action_add()
    {
        $this->action_edit();
        $this->edit = 0;
        $this->tax = '';
    }
    
    function _readTaxForm()
    {
        if (isset($_REQUEST["ind"])) {
            $ind = $_REQUEST["ind"];
            $this->ind = $ind;
            $this->taxes = new XLite_Model_TaxRates();
            if ($ind === '') {
                $ind = array();
            } else {    
                $ind = explode(',',$ind);
            }    
            $this->indexes = $ind;
        }

        $this->initRuleParams();
        $conjuncts = array();
        foreach ($this->taxParams as $param) {
            if (!isset($_POST[$param->var])) {
                continue;
            }
            if (trim($_POST[$param->var]) !== '') {
                $conjuncts[] = "$param->cond=".$_POST[$param->var];
            }
        }
        $condition = join(' AND ', $conjuncts);
        $action = '';
        $taxValue = trim($_POST["taxValue"]);
        $taxName = trim($_POST["taxName"]);
        if ($taxName !== '' && $taxValue !== '') {
            if (is_numeric($taxValue)) {
                $action = "$taxName:=$taxValue";
                if(is_array($this->tax)){
                    $currentName = substr($this->tax['action'], 0, strpos($this->tax['action'], ":="));
                } else {
                    $currentName = substr($this->tax, 0, strpos($this->tax, ":="));
                }
                
                $tax = new XLite_Model_TaxRates();
                if($currentName != '' && $currentName <> $taxName && $tax->isUsedInExpressions($currentName, $taxName)){
                    $this->set('error', 'Tax name "' . $currentName . '" is used in another formula.');
                    return null;
                }

            } elseif ($taxValue{0} == '=') {
                // check expression {{{
                $invalids = array();
                $tax = new XLite_Model_TaxRates();
                if ($tax->checkExpressionSyntax($taxValue, $invalids, $taxName)) {
                    $action = "$taxName:=$taxValue";
                } else {
                    $this->set('inv_exp_error', 1);
                    $this->set('exp_error', '"<b>'.join('</b>", "<b>', $invalids).'</b>"');
                    return null;
                }
                $currentName = '';

                if(is_array($this->tax)){
                    $currentName = substr($this->tax['action'], 0, strpos($this->tax['action'], ":="));
                } else {
                    $currentName = substr($this->tax, 0, strpos($this->tax, ":="));
                }

                if($currentName != '' && $currentName <> $taxName && $tax->isUsedInExpressions($currentName, $taxName)){
                    $this->set('error', 'Tax name "' . $currentName . '" is used in another formula.');
                    return null;
                }
                // }}}
            } else {
                $this->error = "Tax value must be a number or contain '=' at its start: '$taxValue'";
                return null;
            }
        }
        if ($action !== '' && $condition === '') {
            return $action;
        }
        if ($action !== '' && $condition !== '') {
            return array("condition" => $condition, "action" => $action);
        }
        if ($action === '' && $condition !== '') {
            return array("condition" => $condition, "action" => array(), "open" => true);
        }
        $this->error = "Form is empty";
        return null;
    }
    
    function action_add_submit()
    {
        $node = $this->_readTaxForm();
        
        if (is_null($node)) {
            $this->valid = false;
            $this->action_add(); // show errors and the form again
        } else {
            $subTree = $this->locateNode($this->taxes->_rates, $this->indexes);
            if (isset($subTree["action"])) {
                $subTree["action"][] = $node;
            } else {
                $subTree[] = $node;
            }
            // store
            $this->taxes->setSchema(array("tax_rates" => $this->taxes->_rates));
            if ($_POST["add_another"]) {
                $this->set('returnUrl', 'admin.php?target=taxes&page=add_rate&mode=add');
                $this->set("mode", "add");
            } else {
                $this->set("mode", "");
            }
        }
    }

    function action_edit_submit()
    {
        $node = $this->_readTaxForm();
        if (is_null($node)) {
            $this->valid = false;
            $this->action_edit(); // show errors and the form again
        } else {
	        $subTree = $this->locateNode($this->taxes->_rates, $this->indexes);
			if (empty($node['action']))	
				$action = $subTree['action'];
            $subTree = $node;
 	        if (!empty($action))
				$subTree['action'] = $action;
            // store
            $this->taxes->setSchema(array("tax_rates" => $this->taxes->_rates));
            $this->set("mode", "");
        }
    }

    function action_delete_rate()
    {
        $ind = $this->_levels[$_REQUEST["ind"]];
        $subTreeIndex = $ind;
        $lastIndex = array_pop($subTreeIndex); // remove last
        $subTree = $this->locateNode($this->taxes->_rates, $subTreeIndex);
        if (isset($subTree[$lastIndex])) {
            unset($subTree[$lastIndex]);
        }
        if (isset($subTree["action"][$lastIndex])) {
            unset($subTree["action"][$lastIndex]);
        }
        // store
        $this->taxes->setSchema(array("tax_rates" => $this->taxes->_rates));

        $this->set("mode", "");
    }

    function getTaxCondParam($node, $param)
    {
        if (is_array($node)) {
            $cond = $node["condition"];
            $taxParams = $this->taxes->_parseCondition($cond);
            if (isset($taxParams[$param])) {
                return $taxParams[$param];
            }    
        }
        return null;
    }
    
    function initRuleParams()
    {
        $countries = new XLite_Base();
        $countries->name = 'Countries';
        $countries->var  = 'country';
        $countries->cond  = 'country';
        $countries->values = array("EU country");
        $c = new XLite_Model_Country();
        foreach ($c->findAll() as $country) {
            $countries->values[] = $country->get("country");
        }
        $states = new XLite_Base();
        $states->name = 'States';
        $states->var  = 'state';
        $states->cond  = 'state';
        $states->values = array();
		$states->diplay_ex = 1;
        $c = new XLite_Model_State();

		$lit = true;
		$last_ccode = "";
        foreach ($c->findAll(null, "country_code, state") as $state) {
			$country_code = $state->get("country_code");
			$country = "";
			if ( $country_code != $last_ccode ) {
				$lit = !$lit;
				$last_ccode = $country_code;
				$c = new XLite_Model_Country($country_code);
				$country = $c->get("country");
			}

            $states->values[] = array("val"=>$state->get("state"), "code"=>$state->get("code"), "country"=>$country, "lit"=>( $lit ) ? 1 : 0);
        }

        $cities = new XLite_Base();
        $cities->name = "Cities";
        $cities->var = "city";
        $cities->cond = "city";
        $cities->values = array();
        $pr = new XLite_Model_Profile();
        foreach ($pr->findAll() as $p) {
            $cities->values[] = $p->get("shipping_city");
        }
        $cities->values = array_unique($cities->values);
        if (isset($cities->values[''])) {
            unset($cities->values['']);
        }

        $pm = new XLite_Base();
        $pm->name = "Payment method";
        $pm->var = "pm";
        $pm->cond = "payment method";
        $pm->values = array();
        $pmethod = new XLite_Model_PaymentMethod();
        $methods = $pmethod->getActiveMethods();
        foreach ($methods as $method) {
            $pm->values[] = $method->get("name");
        }
        
        $classes = new XLite_Base();
        $classes->name = "Product class, either new or existing";
        $classes->var = "pclass";
        $classes->cond = "product class";
        $classes->values = array_unique(array_merge(array("shipping service"), $this->taxes->getProductClasses()));
        array_multisort($classes->values);

        $memberships = new XLite_Base();
        $memberships->name = "User membership level";
        $memberships->var = "membership";
        $memberships->cond = "membership";
        $memberships->values = array("No membership");
        
        $memberships->values = array_merge($memberships->values, $this->config->Memberships->memberships);

        $zips = new XLite_Base();
        $zips->name = "Zip codes/ranges (e.g. 43200-43300,55555)";
        $zips->var = "zip";
        $zips->cond = "zip";
        $zips->values = array();
        
        $this->taxParams = array($countries,$states,$cities,$pm,$classes,$memberships, $zips);
        $this->taxNames = $this->taxes->getTaxNames();
    }

    function _sortRates(&$rateTree, &$pos)
    {
        for ($i=0; $i<count($rateTree); $i++) {
            // sort children
            if (is_array($rateTree[$i]) && is_array($rateTree[$i]["action"])) {
                if (!isset($pos[$i])) {
                    continue;
                }    
                $this->_sortRates($rateTree[$i]["action"], $pos[$i]);
            }  
        }
        if (!is_array($pos["orderbys"])) {
            print "pos = "; print_r($pos);
            $this->_die("pos['orderbys'] must be an array");
        }
        $ratesToSort = $rateTree;
        array_multisort($pos["orderbys"], $ratesToSort);
        $rateTree = $ratesToSort;
    }
    
    function locateNode(&$tree, $path)
    {
        $ptr = $tree;
        foreach ($path as $index) {
            if (isset($ptr["action"])) {
                $ptr = $ptr["action"];
            }
            if (!isset($ptr[$index])) {
                // create a node 
                $ptr[$index] = array();
            }
            $ptr = $ptr[$index];
        }
        return $ptr;
    }
    
    function _insertValue($expr, $value)
    {
        list($name,$oldval) = explode(':=', $expr);
        if (!isset($oldval)) {
            $this->_die("expr=$expr - wrong format");
        }
        return "$name:=$value";
    }
    
    function getIndex($tax, $ind)
    {
		return (empty($tax["pos"]) ? ($ind + 1) * 10 : $tax["pos"]);
    }

    function getPath($ind)
    {
        return join(',', $this->_levels[$ind]);
    }

    function getTaxName($tax)
    {
        return $tax["name"];
    }
	
	function getRegistration($tax)
	{
		return $tax["registration"];
	}
	
    function getNoteTaxName($node)
    {
        if (!isset($_POST['taxName'])) {
            if (is_array($node)) {
                $node = $node["action"];
                if (is_array($node)) {
                    return '';
                }
            }
            return $this->getVarName($node);
        } else {
            return $_POST['taxName'];
        }
    }

    function getNoteTaxValue($node)
    {
        if (!isset($_POST['taxValue'])) {
            if (is_array($node)) {
                $node = $node["action"];
                if (is_array($node)) {
                    return '';
                }
            }
            return $this->getVarValue($node);
        } else {
            return $_POST['taxValue'];
        }
    }

    function getVarName($expr)
    {
        list($name) = explode(':=', $expr);
        return $name;
    }

    function getCondVarName($expr)
    {
        $expr = $expr["action"];
        list($name) = explode(':=', $expr);
        return $name;
    }

    function getVarValue($expr)
    {
        list($name,$value) = explode(':=', $expr);
        return $value;
    }

    function getCurrentVarValue($ind, $expr)
    {
        return (
            $this->isInvalidExp($ind) && isset($_POST["varvalue"]) && isset($_POST["varvalue"][$ind]) 
            ? $_POST["varvalue"][$ind]
            : $this->getVarValue($expr)
        );
    }

    function getCondVarValue($expr)
    {
        $expr = $expr["action"];
        list($name,$value) = explode(':=', $expr);
        return $value;
    }

    function getCurrentCondVarValue($ind, $expr)
    {
        return (
            $this->isInvalidExp($ind) && isset($_POST["varvalue"]) && isset($_POST["varvalue"][$ind]) 
            ? $_POST["varvalue"][$ind]
            : $this->getCondVarValue($expr)
        );
    }

    function getCondParam($node, $param, $name=null)
    {
        if ($this->edit && $name && !isset($_POST[$name])) {
            if (is_array($node)) {
                $cond = $this->taxes->_parseCondition($node["condition"]);
                if (isset($cond[$param])) {
                    return join(',', $cond[$param]);
                }
            }
            return '';
        } else {
            return $_POST[$name]; 
        }
    }

    function getDisplayName($tax)
    {
        return $tax["display_label"];
    }

    function getRates()
    {
        $ind = 0;
        $this->_rates = array();
        $this->_levels = array();
        $this->_maxLevel = 0;
        $this->_initRates($this->taxes->_rates, array(), $ind);
    }

    function _initRates($rates, $levels, &$ind)
    {
        if ($this->_maxLevel < count($levels)) {
            $this->_maxLevel = count($levels);
        }
        if (!is_array($rates)) {
        
            $this->_die ("rates='$rates' must be array");
        }
        foreach ($rates as $ind_rate => $rate) {
            $this->_rates[$ind] = $rate;
            $this->_levels[$ind] = $levels;
            $this->_levels[$ind][] = $ind_rate;
            $ind++;
            if (is_array($rate)) {
                if (is_array($rate["action"]) && isset($rate["open"])) {
                    $levels1 = $levels;
                    $levels1[] = $ind_rate;
                    $this->_initRates($rate["action"], $levels1, $ind);
                }    
            }
        }
    }

    function isOpen($row)
    {
        return isset($row["open"]);
    }

    function getLevels($ind)
    {
        $result = "";
        $count = count($this->_levels[$ind])-1;
        for ($i=0; $i<$count; $i++) {
            $result .= "<td width=\"35\"></td>";
        }
        return $result;
    }

    function getCondition($cond)
    {
        return $cond["condition"];
    }

    function isAction($a) 
    {
        return is_scalar($a);
    }

    function isCondition($a)
    {
        return is_array($a) && is_array($a["action"]);
    }

    function isConditionalAction($a)
    {
        return is_array($a) && is_scalar($a["action"]);
    }    

    function getColspan($ind, $additional=1)
    {
        return $this->_maxLevel-count($this->_levels[$ind])+$additional+1;
    }

    function getMaxColspan($additional=0)
    {
        return $this->_maxLevel+$additional;
    }

    function getTreePos($ind)
    {
        $levels = $this->_levels[$ind];
        return $levels[count($levels)-1]*10 + 10;
    }

    function getHeaderMargin()
    {
        $result = '';
        for ($i=0; $i<$this->_maxLevel; $i++) {
            $result .= "<th></th>";
        }    
        return $result;
    }

    function isLast($ind)
    {
        return !isset($this->_levels[$ind+1]) || count($this->_levels[$ind+1]) < count($this->_levels[$ind]);
    }

	function action_calculator()
	{
        if (!empty($_POST)) {
            $_POST["country"] = $_POST["billing_country"];
            unset($_POST["billing_country"]);
            $_POST["state"] = $_POST["billing_state"];
            unset($_POST["billing_state"]);
            $this->set("properties", $_POST);

            $tax = new XLite_Model_TaxRates();
		    // setup tax rate calculator
		    if (!is_array($tax->_conditionValues)) {
		    	$tax->_conditionValues = array();
		    }
		    foreach($_POST as $name => $value) {
			    $name1 = str_replace("_", " ", $name);
			    $tax->_conditionValues[$name1] = $this->$name;
		    }
		    if (isset($this->country)) {
			    $country = new XLite_Model_Country($this->country);
			    $tax->_conditionValues["country"] = $country->get("country");
        	    if ($country->isEUMember()) {
        		    $tax->_conditionValues["country"] .= ",EU country";
              	}
		    }
		    if (isset($this->state)) {
			    $state = new XLite_Model_State($this->state);
			    $tax->_conditionValues["state"] = $state->get("state");
    		}

	    	// calculate taxes
		    $tax->calculateTaxes();
		    $this->item_taxes = $tax->_taxValues;
            foreach ($this->item_taxes as $taxkey => $taxvalue) {
                $this->item_taxes[$taxkey] = $tax->_calcFormula($taxvalue);
            }
		    $tax->_conditionValues['product class'] = 'shipping service';
		    $tax->calculateTaxes();
		    $this->shipping_taxes = $tax->_taxValues;
            foreach ($this->shipping_taxes as $taxkey => $taxvalue) {
                $this->shipping_taxes[$taxkey] = $tax->_calcFormula($taxvalue);
            }
            $this->set('display_taxes', 1);
        }
        
        // show tax calculator
        $w = new XLite_View_Abstract();
        $w->component = $this;
        $w->set("template", "tax/calculator.tpl");
        $w->init();
        $w->display();
        // do not output anything
        $this->set("silent", true);
	}

	function isDoubleValue($value)
	{
		if (strcmp(strval(doubleval($value)), strval($value)) == 0)
		{
			return true;
		}
		return false;
	}

    function action_save()
    {
        $name = $this->get("save_schema");
        if ($name == "") {
            $name = $this->get("new_name");
        }

        $tax = new XLite_Model_TaxRates();
        $tax->saveSchema($name);
    }

    function action_export()
    {
        $name = $this->get("export_schema");
        $tax = new XLite_Model_TaxRates();
        $schema = $tax->get("predefinedSchemas.$name");
        if (!is_null($schema)) {
            $this->set("silent", true);
            $this->startDownload("$name.tax");
            print serialize($schema);
        }
    }

    function action_import()
    {
        if (!$this->checkUploadedFile()) {
        	$this->set("valid", false);
        	$this->set("invalid_file", true);
        	return;
        }

        $file = $this->get("uploadedFile");
        if (is_null($file)) {
            return;
        }    
        $name = basename($_FILES['userfile']['name'], ".tax");
        $schema = unserialize(file_get_contents($file));
        $tax = new XLite_Model_TaxRates();
        $tax->saveSchema($name, $schema);
    }

    function getEdit()
    {
        return $this->get("mode") == "edit";
    }

	function getSchemas()
	{
		$schemas = unserialize($this->xlite->config->Taxes->schemas);
		return ($schemas ? $schemas : array());
	}

    function getCountriesStates()
    {
    	if (!isset($this->_profileDialog)) {
    		$this->_profileDialog = new XLite_Controller_Admin_Profile();
    	}
        return $this->_profileDialog->getCountriesStates();
    }

    function isInvalidExp($ind)
    {
        return isset($this->invalidExpressions[$ind]) || isset($this->invalidFormula[$ind]);
    }

    function getExpInvalidVar($ind)
    {
        return (isset($this->invalidExpressions[$ind]) ? $this->invalidExpressions[$ind] : '???');
    }

	function getInvalidFormula($ind)
	{
        return (isset($this->invalidFormula[$ind]) ? $this->invalidFormula[$ind] : '???');
	}
}

// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
