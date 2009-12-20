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

/* vim: set expandtab tabstop=4 softtabstop=4 foldmethod=marker shiftwidth=4: */

define('TAX_TOLOWERCASE', 1);

/**
* Calculate tax rates based on order parameters (location, 
* products)
* To calculate taxes on a item, do teh following:
* $tax =& func_new("TaxRates");
* $tax->setOrder($order); // set customer location info
* $tax->setOrderItem($orderItem); // order item info
* $tax->calculateTaxes();
* $taxes = $tax->getAllTaxes(); // associative array of taxes
*
* To calculate tax on shipping, do the following:
* $tax->_conditionValues["product class"] = "shipping service";
* $tax->calculateTaxes();
* $taxes = $tax->getAllTaxes(); // associative array of taxes
*
* You can setup the following condition values:
* country, state, city, membership, product class, payment method
*
* @package Kernel
* @access public
* @version $Id$
*/
class TaxRates extends Object
{
    var $_rates;
    /**
    * "default tax schema name" => array of rate rules
    */
    var $_predefinedSchemas = array();
    var $_taxValues = array();
    var $_conditionValues = array();
    
    function constructor()
    {
        parent::constructor();
        $this->_createOneGlobalTax();
        $this->_createUSStateRates();
        $this->_createVatTax();
        $this->_createCanadianTax();
        $this->_init();
    }

    function _init()
    {
        if (strlen($this->config->get("Taxes.tax_rates"))>0) {
            $this->_rates = unserialize($this->config->get("Taxes.tax_rates"));
        } else {
            $this->_rates = array();
        }
		if (!is_array($this->_rates)) {
            $this->_rates = array();
		}

        if (strlen($this->config->get("Taxes.taxes"))>0) {
            $this->_taxes = unserialize($this->config->get("Taxes.taxes"));
        } else {
            $this->_taxes = array();
        }
		if (!is_array($this->_taxes)) {
            $this->_taxes = array();
		}
    }
   
    function _createOneGlobalTax()
    {
        $this->_predefinedSchemas["One global tax value"] = array(
"taxes" => array(array("name" => "Tax", "display_label" => "Tax")),
"prices_include_tax" => "",
"include_tax_message" => "",
"tax_rates" => array("Tax:=0", 
    array("condition"=>"product class=shipping service", "action"=>"Tax:=0"),
    array("condition"=>"product class=Tax free", "action"=>"Tax:=0")
    )
        );
    }
    
    function _createUSStateRates() // {{{
    {
        // create pre-defined tax rules
        $this->_predefinedSchemas["US state sales tax rates"] = array(
"use_billing_info" => "N", 
"taxes" => array(array("name" => "Tax", "display_label" => "Tax")),
"prices_include_tax" => "",
"include_tax_message" => "",
"tax_rates" => array(
"Tax:=0",
array("condition" => "country=United States", "action" => array(
"City tax:=0",
"State tax:=0",
"Tax:==State tax + City tax",
array("condition" => "state=Alabama", "action" => array(
    "State tax:=4",
    array("condition"=>"product class=Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Arizona", "action" => array(
    "State tax:=5.6",
    array("condition"=>"product class=Food,Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Arkansas", "action" => array(
    "State tax:=5.125",
    array("condition"=>"product class=Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=California", "action" => array(
    "State tax:=7.25",
    array("condition"=>"product class=Food,Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Colorado", "action" => array(
    "State tax:=2.9",
    array("condition"=>"product class=Food,Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Connecticut", "action" => array(
    "State tax:=6",
    array("condition"=>"product class=Food,Prescription Drugs,Non-prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Florida", "action" => array(
    "State tax:=6",
    array("condition"=>"product class=Food,Prescription Drugs,Non-prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Georgia", "action" => array(
    "State tax:=4",
    array("condition"=>"product class=Food,Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Hawaii", "action" => array(
    "State tax:=4",
    array("condition"=>"product class=Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Idaho", "action" => array(
    "State tax:=4",
    array("condition"=>"product class=Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Illinois", "action" => array(
    "State tax:=6.25",
    array("condition"=>"product class=Food,Prescription Drugs,Non-prescription Drugs", "action"=>"State tax:=1"),
    )),
array("condition" => "state=Indiana", "action" => array(
    "State tax:=6",
    array("condition"=>"product class=Food,Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Iowa", "action" => array(
    "State tax:=5",
    array("condition"=>"product class=Food,Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Kansas", "action" => array(
    "State tax:=5.3",
    array("condition"=>"product class=Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Kentucky", "action" => array(
    "State tax:=6",
    array("condition"=>"product class=Food,Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Louisiana", "action" => array(
    "State tax:=4",
    array("condition"=>"product class=Food", "action"=>"State tax:=2"),
    array("condition"=>"product class=Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Maine", "action" => array(
    "State tax:=5",
    array("condition"=>"product class=Food,Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Maryland", "action" => array(
    "State tax:=5",
    array("condition"=>"product class=Food,Prescription Drugs,Non-prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Massachusetts", "action" => array(
    "State tax:=5",
    array("condition"=>"product class=Food,Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Michigan", "action" => array(
    "State tax:=6",
    array("condition"=>"product class=Food,Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Minnesota", "action" => array(
    "State tax:=6.5",
    array("condition"=>"product class=Food,Prescription Drugs,Non-prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Mississippi", "action" => array(
    "State tax:=7",
    array("condition"=>"product class=Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Missouri", "action" => array(
    "State tax:=4.225",
    array("condition"=>"product class=Food", "action"=>"State tax:=1.225"),
    array("condition"=>"product class=Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Nebraska", "action" => array(
    "State tax:=5.5",
    array("condition"=>"product class=Food,Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Nevada", "action" => array(
    "State tax:=6.5",
    array("condition"=>"product class=Food,Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=New Jersey", "action" => array(
    "State tax:=6",
    array("condition"=>"product class=Food,Prescription Drugs,Non-prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=New Mexico", "action" => array(
    "State tax:=5",
    array("condition"=>"product class=Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=New York", "action" => array(
    "State tax:=4",
    array("condition"=>"product class=Food,Prescription Drugs,Non-prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=North Carolina", "action" => array(
    "State tax:=4.5",
    array("condition"=>"product class=Food,Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=North Dakota", "action" => array(
    "State tax:=5",
    array("condition"=>"product class=Food,Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Ohio", "action" => array(
    "State tax:=5",
    array("condition"=>"product class=Food,Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Oklahoma", "action" => array(
    "State tax:=4.5",
    array("condition"=>"product class=Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Pennsylvania", "action" => array(
    "State tax:=6",
    array("condition"=>"product class=Food,Prescription Drugs,Non-prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Rhode Island", "action" => array(
    "State tax:=7",
    array("condition"=>"product class=Food,Prescription Drugs,Non-prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=South Carolina", "action" => array(
    "State tax:=5",
    array("condition"=>"product class=Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=South Dakota", "action" => array(
    "State tax:=4",
    array("condition"=>"product class=Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Tennessee", "action" => array(
    "State tax:=7",
    array("condition"=>"product class=Food", "action"=>"State tax:=6"),
    array("condition"=>"product class=Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Texas", "action" => array(
    "State tax:=6.25",
    array("condition"=>"product class=Food,Prescription Drugs,Non-prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Utah", "action" => array(
    "State tax:=4.75",
    array("condition"=>"product class=Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Vermont", "action" => array(
    "State tax:=5",
    array("condition"=>"product class=Food,Prescription Drugs,Non-prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Virginia", "action" => array(
    "State tax:=4.5",
    array("condition"=>"product class=Food", "action"=>"State tax:=4"),
    array("condition"=>"product class=Prescription Drugs,Non-prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Washington", "action" => array(
    "State tax:=6.5",
    array("condition"=>"product class=Food,Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=West Virginia", "action" => array(
    "State tax:=6",
    array("condition"=>"product class=Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Wisconsin", "action" => array(
    "State tax:=5",
    array("condition"=>"product class=Food,Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=Wyoming", "action" => array(
    "State tax:=4",
    array("condition"=>"product class=Prescription Drugs", "action"=>"State tax:=0"),
    )),
array("condition" => "state=District of Columbia", "action" => array(
    "State tax:=5.75",
    array("condition"=>"product class=Food,Prescription Drugs,Non-prescription Drugs", "action"=>"State tax:=0"),
    ))),"open" => true), 
    // no tax on shipping
    array("condition"=>"product class=shipping service", "action"=>"Tax:=0"),
    array("condition"=>"product class=Tax free", "action"=>"Tax:=0")
    ));
    } // }}}

    function _createVatTax() // {{{
    {
         $this->_predefinedSchemas["VAT system"] = array
         (
			"taxes" => array
			(
				array
				(
					"name" => "VAT", 
					"display_label" => "VAT"
				),
			),
			"prices_include_tax" => "Y",
			"include_tax_message" => ", including VAT",
			"tax_rates" => array
			(
				"Tax:==VAT", 
				"VAT:=0", 
				array
				(
					"condition"=>"country=EU country", 
					"action"=> array
					(
						"VAT:=17.5",  
						array
						(
							"condition"=>"product class=shipping service", 
							"action"=>"VAT:=17.5"
						),
  						array
  						(
  							"condition"=>"product class=5% VAT", 
  							"action"=>"VAT:=5"
  						),
  						array
  						(
  							"condition"=>"product class=VAT exempt", 
  							"action"=>"VAT:=0"
  						),
					), 
					"open" => true
				)
			)
		);
         $this->_predefinedSchemas["VAT system (alternative)"] = array
         (
			"taxes" => array
			(
				array
				(
					"name" => "VAT", 
					"display_label" => "VAT (products)"
				),
				array
				(
					"name" => "SVAT", 
					"display_label" => "VAT (shipping)"
				),
			),
			"prices_include_tax" => "Y",
			"include_tax_message" => ", including VAT",
			"tax_rates" => array
			(
				"Tax:==VAT", 
				"VAT:=0", 
				array
				(
					"condition"=>"country=EU country", 
					"action"=> array
					(
						"VAT:=17.5",  
						array
						(
							"condition"=>"product class=shipping service", 
							"action"=>"VAT:=0"
						),
  						array
  						(
  							"condition"=>"product class=5% VAT", 
  							"action"=>"VAT:=5"
  						),
  						array
  						(
  							"condition"=>"product class=VAT exempt", 
  							"action"=>"VAT:=0"
  						),
						array
						(
							"condition"=>"product class=shipping service", 
							"action"=>"SVAT:=17.5"
						),
						array
						(
							"condition"=>"product class=shipping service", 
							"action"=>"Tax:==SVAT"
						),
					), 
					"open" => true
				),
			)
		);
    } // }}}

    function _createCanadianTax() // {{{
    {
        $this->_predefinedSchemas["Canadian GST/PST system"] = array(
"taxes" => array(
    array("name" => "GST", "display_label" => "GST"),
    array("name" => "HST", "display_label" => "HST"),
    array("name" => "PST", "display_label" => "PST")),
"prices_include_tax" => "",
"include_tax_message" => "",
"tax_rates" => array("Tax:==GST+PST",
array("condition" => "country=Canada", "open" => true, "action" => array(
    array("condition"=>"product class=shipping service", "action" => "Tax:==GST"),
    // HST states
    array("condition"=>"state=Newfoundland,Labrador,Nova Scotia,New Brunswick", "action" => array(
        "HST:=15", 
        array("condition"=>"product class=shipping service", "action" => "Tax:==HST")
    )), // /HST states
    array("condition"=>"state=Quebec", "action" => array(
        "GST:=6", "PST:=7.5", "Tax:==GST+(1+GST/100.0)*PST")),
    array("condition"=>"state=Ontario", "action" => array("GST:=6", "PST:=8")),
    array("condition"=>"state=Manitoba", "action" => array("GST:=6", "PST:=7")),
    array("condition"=>"state=Saskatchevan", "action" => array("GST:=6", "PST:=6")),
    array("condition"=>"state=British Columbia", "action" => array("GST:=6", "PST:=7.5")))),
array("condition" => "country=New Zealand", "action" => array("Tax:==GST", "GST:=12.5")),
array("condition" => "country=Australia", "action" => array("Tax:==GST", "GST:=10")))
    );
    } // }}}

    function setPredefinedSchema($name)
    {
        $schemas = $this->get("predefinedSchemas");
        $schema = $schemas[$name];
        $this->setSchema($schema);
    }

    function setSchema($schema)
    {
    	if (is_array($schema)) {
            $conf =& func_new("Config");
            $conf->set("category", "Taxes");
            foreach ($schema as $name => $value) {
                $conf->set("name", $name);
                if (!is_scalar($value)) {
                    $value = serialize($value);
                }
                $conf->set("value", $value); 
                $this->config->set("Taxes.$name", $value);
                $conf->update();
            }
        }
        $this->_init();
    }
    
    function setOrder($order)
    {
        $profile = $order->get("profile");
        if (!is_null($profile)) {
			$this->set("profile", $profile);
        } else {
        	if ($this->config->get("General.def_calc_shippings_taxes")) {
                $default_country =& func_new("Country", $this->config->get("General.default_country"));
    			$this->_conditionValues["country"] = $default_country->get("country");
        		if ($default_country->isEUMember()) {
        			$this->_conditionValues["country"] .= ",EU country";
        		}
        	}
        }
        if (!is_null($order->get("paymentMethod"))) {
            $this->_conditionValues["payment method"] = $order->get("paymentMethod.name");
        }
    }

	function setProfile($profile)
	{
		if ($this->config->get("Taxes.use_billing_info")) {
			$this->_conditionValues["state"] = $profile->get("billingState.state");
			$this->_conditionValues["country"] = $profile->get("billingCountry.country");
			$countryCode = $profile->get("billing_country");
			$this->_conditionValues["city"] = $profile->get("billing_city");
			$this->_conditionValues["zip"] = $profile->get("billing_zipcode");
		} else { // shipping destination
			$this->_conditionValues["state"] = $profile->get("shippingState.state");
			$this->_conditionValues["country"] = $profile->get("shippingCountry.country");
			$countryCode = $profile->get("shipping_country");
			$this->_conditionValues["city"] = $profile->get("shipping_city");
			$this->_conditionValues["zip"] = $profile->get("shipping_zipcode");
		}
		$c =& func_new("Country",$countryCode);
		if ($c->isEUMember()) {
			$this->_conditionValues["country"] .= ",EU country";
		}
		$this->_conditionValues["membership"] = $profile->get("membership");
		if ($this->_conditionValues["membership"] == "") {
			$this->_conditionValues["membership"] = "No membership";
		}
	}

    function setOrderItem($item)
    {
        if (!is_null($item->get("product"))) {
            $this->_conditionValues["product class"] = $item->get("product.tax_class");
            // categories
            $categories = array();
            foreach ($item->get("product.categories") as $category) {
                $categories[] = $category->get("category_id");
            }
            $this->_conditionValues["category"] = join(',', $categories);
        }
		if (!$this->config->get("Taxes.prices_include_tax")) {
			$this->_conditionValues["cost"] = $item->get("taxableTotal");
		} else {
        	$this->_conditionValues["cost"] = $item->get("price");
        	$this->_conditionValues["amount"] = $item->get("amount");
        }
    }

    function calculateTaxes()
    {
        $this->_taxValues = array();
        $this->_interpretAction($this->_rates);
    }
    
    function getTaxRate($name)
    {
        return $this->_calcFormula($this->_taxValues[$name]);
    }

    function _interpretAction($action)
    {
        if (is_array($action)) {
            if (isset($action["condition"])) {
                // interpret conditional taxes
                if ($this->_interpretCondition($action["condition"])) {
                    $this->_interpretAction($action["action"]);
                }
            } else {
                foreach ($action as $rate) {
                    $this->_interpretAction($rate);
                }
            }    
        } else {
            // tax-name:=value|=expression syntax
            list($tax, $value) = explode(':=', $action);
            $this->_taxValues[trim($tax)] = trim($value);
        }
    }

    function _calcFormula($expression)
    {
        if ($expression{0} == '=') {
            $expression = substr($expression,1);
            // first, replace all names with $this->_conditionValues[name]
            $sortedValues = array();
            $values = $this->_taxValues;
            for ($i=0; $i<count($this->_taxValues); $i++) {
                $maxName = "";
                foreach ($values as $name => $value) {
                    if (strlen($maxName) < strlen($name)) {
                        $maxName = $name;
                    }
                }
                if ($maxName != "") {
                    $sortedValues[$maxName] = $values[$maxName];
                    if (isset($values[$maxName])) {
                    	unset($values[$maxName]);
                    }
                }
            }
            foreach ($sortedValues as $name => $value) {
                $pattern = '/\b'.stripslashes($name).'\b/';
                if (preg_match($pattern, stripslashes($expression))) {

					// eternal recursion protection: 
					// if the tax value depends on itself, then don't continue recursive calculation
					static $searchingForNames;
					if (!is_array($searchingForNames)) $searchingForNames = array();
					if (isset($searchingForNames[$name])) return 0;
					$searchingForNames[$name] = 1;

                    $expression = preg_replace($pattern, $this->_calcFormula($value), $expression);
					unset($searchingForNames[$name]);
                }
            }
            $expression = preg_replace('/(?=\b\D)[ \w]+\b/', '0', $expression);
            $value = 0;
            @eval('$value='.$expression.';');
            return $value;
        } else {
            return $expression;
        }
    }

    function _interpretCondition($cond)
    {
        $conjuncts = $this->_parseCondition($cond, TAX_TOLOWERCASE);
        foreach ($conjuncts as $param => $values) {
            if (!isset($this->_conditionValues[$param])) {
                return false;
            }
            $orderValues = explode(',',strtolower(trim($this->_conditionValues[$param])));
            // search for value(s)
            $found = array_intersect($orderValues, $values);
            if (!count($found)) {
                if ($param == "zip") {
                    // compare zip codes
                    if ($this->_compareZip($orderValues[0], $values)) {
                        return true;
                    }
                }
                return false;
            }
        }
        return true;
    }
    function _compareZip($zip, $ranges)
    {
        foreach ($ranges as $r) {
            if (strpos($r, '-')) {
                list ($start, $end) = explode('-', $r);
                if ($zip<=$end && $zip>=$start) {
                    return true;
                }
            } else {
                if ($r == $zip) {
                    return true;
                }
            }
        }
        return false;
    }

    function getAllTaxes()
    {
    	$this->_shippingTaxes = array();

        $taxes = array();
        foreach ($this->_taxValues as $name => $percent) {
            $percent = $this->_calcFormula($percent);
			if (isset($this->_conditionValues["cost"])) {
				$tax = $this->_conditionValues["cost"] * $percent / 100.0;
				if ($this->config->get("Taxes.prices_include_tax")) {
					$tax = $this->formatCurrency($tax);
					if (isset($this->_conditionValues["amount"]) && $this->_conditionValues["product class"] != "shipping service") {
						$tax = $this->formatCurrency($tax * $this->_conditionValues["amount"]);
					}
				} else {
					$tax = $this->formatCurrency($tax);
				}
			} else {
                $tax = 0;
            }
            if (isset($this->_conditionValues["product class"]) && $this->_conditionValues["product class"] == "shipping service") {
            	$this->_shippingTaxes[$name] = $tax;
            }
            $taxes[$name] = $tax;
        }
        return $taxes;
    }

    function getShippingTaxes()
    {
    	if (isset($this->_shippingTaxes)) {
    		return $this->_shippingTaxes;
    	}

    	$this->_shippingTaxes = array();

        foreach ($this->_taxValues as $name => $percent) {
            $percent = $this->_calcFormula($percent);
			if (isset($this->_conditionValues["cost"])) {
				$tax = $this->_conditionValues["cost"] * $percent / 100.0;
			} else {
                $tax = 0;
            }
			$tax = $this->formatCurrency($tax);
            if (isset($this->_conditionValues["product class"]) && $this->_conditionValues["product class"] == "shipping service" && $name != "Tax") {
            	$this->_shippingTaxes[$name] = $tax;
            }
        }

        return $this->_shippingTaxes;
    }

    function getShippingDefined()
    {
    	$productClasses = $this->getProductClasses();
    	$isShippingDefined = array_search("shipping service", $productClasses);
    	if (!($isShippingDefined === false || is_null($isShippingDefined))) {
    		return true;
    	} else {
    		return false;
    	}
    }

    function getTaxLabel($name)
    {
        foreach ($this->_taxes as $tax) {
            if ($tax["name"] == $name) {
                return $tax["display_label"];
            }
        }
        return '';
    }
	
	function getRegistration($name)
	{
        foreach ($this->_taxes as $tax) {
            if ($tax["name"] == $name) {
                return $tax["registration"];
            }
        }
        return '';
	}

    function getTaxPosition($name)
    {
        foreach ($this->_taxes as $pos => $tax) {
            if ($tax["name"] == $name) {
                return $pos;
            }
        }
        return '';
    }

    function &getProductClasses()
    {
        $classes = array();
        $this->_collectClasses($this->_rates, $classes);
        $classes = array_unique($classes);
        return $classes;
    }
    
    function _collectClasses(&$tree, &$classes)
    {
        foreach ($tree as $node) {
            if (is_array($node)) {
                $cond = $this->_parseCondition($node["condition"]);
                if (isset($cond['product class'])) {
                    $classes = array_merge($classes, $cond['product class']);
                }
                $node = $node["action"];
                if (is_array($node)) {
                    $this->_collectClasses($node, $classes);
                }
            }
        }
    }

    function &getActions()
    {
        $actions = array();
        $this->_collectActions($this->_rates, $actions);
        return $actions;
    }

    function _collectActions(&$tree, &$actions)
    {
        foreach ($tree as $node) {
            if (is_array($node)) {
                if (!isset($node["action"])) {
                    continue;
                }
                $action = $node["action"];
                if (is_array($action)) {
                    $this->_collectActions($action, $actions);
                    continue;
                } 
            } else {
                $action = $node;
            }
            list($name, $value) = explode(':=', $action);
            $value = trim($value);
            if($value{0} != '='){
                $value = "=" . $value;
            }
            $actions[] =  trim($value);
        }
    }

    function getTaxNames()
    {
        $names = array();
        $this->_collectNames($this->_rates, $names);
        $names = array_unique($names);
        return $names;
    }

    function getAllTaxNames()
    {
        $names = array();
        $this->_collectNames($this->_rates, $names);
        return $names;
    }

    function _collectNames(&$tree, &$names)
    {
        foreach ($tree as $node) {
            if (is_array($node)) {
                if (!isset($node["action"])) {
                    print_r($tree);
                    $this->_die("Must contain 'action' key");
                }
                $action = $node["action"];
                if (is_array($action)) {
                    $this->_collectNames($action, $names);
                    continue;
                }
            } else {
                $action = $node;
            }
            list($name) = explode(':=',$action);
            $names[] =  trim($name);
        }
    }

    function _parseCondition($cond, $transform = 0)
    {

        $cond = explode(' AND ', $cond);
        $result = array();
        foreach ($cond as $conjunct) {
            if (trim($conjunct) == '') {
                continue;
            }
            list($name, $values) = explode('=', $conjunct);
            if ($transform == TAX_TOLOWERCASE) {
                $values = strtolower($values);
            }
            $values = explode(',', $values);
            for ($i=0; $i<count($values); $i++) {
                $values[$i] = trim($values[$i]);
            }
            $result[trim($name)] = $values;        
        }
        return $result;
    }

    function getPredefinedSchemas()
    {
        $schemas = $this->_predefinedSchemas; // default set
        if (!is_null($this->get("config.Taxes.schemas"))) {
        	$savedSchemas = unserialize($this->get("config.Taxes.schemas"));
        	if (is_array($savedSchemas)) {
                foreach (unserialize($this->get("config.Taxes.schemas")) as $k=>$v) {
                    $schemas[$k] = $v;
                }
            }
        }
        return $schemas;
    }

    function saveSchema($name, $schema = "")
    {
        // Schema includes the following properties
        // 
        // config.taxes
        // config.tax_rates
        // config.use_billing_info
        // config.prices_include_tax
        // config.include_tax_message
        //
        if (!is_null($schema) && $schema == "") {
            $schema = array(
                    "taxes" => unserialize($taxes = $this->get("config.Taxes.taxes")),
                    "tax_rates" => unserialize($this->get("config.Taxes.tax_rates")),
                    "use_billing_info" => $this->get("config.Taxes.use_billing_info") ? "Y" : "N",
                    "prices_include_tax" => $this->get("config.Taxes.prices_include_tax") ? "Y" : "N",
                    "include_tax_message" => $this->get("config.Taxes.include_tax_message"),
                    );    
        }        

        $c =& func_new("Config");
        $c->set("category", "Taxes");
        $c->set("name", "schemas");

        if (is_null($this->get("config.Taxes.schemas"))) {
            // create schemas repositary
            $c->set("value", serialize(array($name => $schema)));
            $c->create();
        } else { 
            // update existing schemas repositary
            $schemas = unserialize($this->get("config.Taxes.schemas"));
            if (is_null($schema)) {
                if (isset($schemas[$name])) {
                	unset($schemas[$name]);
                }
            } else {
                $schemas[$name] = $schema;
           }     
            $c->set("value", serialize($schemas));
            $c->update();
        }
    }

    function formatCurrency($price)
    {
    	if (!isset($this->_BaseObj)) {
    		$this->_BaseObj =& func_new("Base");
    	}

        return $this->_BaseObj->formatCurrency($price);
    }

    /**
    * Check expression
    * @param string $exp    expression
    * @param array  $errors invalid tax names
    * @return true - expression ok
    */
    function checkExpressionSyntax($exp, &$errors, $tax_name = 'Tax')
    {
        if ($exp{0} == '=') {
            $exp = substr($exp, 1);
        } else {
            return false;
        }
        $exp   = ' '.stripslashes($exp).' '; 
        $taxes = $this->getTaxNames();
		if (in_array($tax_name, $taxes)) {
			$index = array_search($tax_name, $taxes);
			unset($taxes[$index]);
		}
        $exp = preg_replace('/\b\d+\b/', '@', $exp); // remove all numbers
		// remove all tax names
        foreach ($taxes as $t) {
            $exp = preg_replace('/\b'.stripslashes($t).'\b/', "@", $exp);
        }
        $tmp = preg_split("/[^ \w]+/", $exp, -1, PREG_SPLIT_NO_EMPTY);

        $errors = array();
        for ($i = 0; $i < count($tmp); $i++) {
            if (trim($tmp[$i])) {
                $errors[] = str_replace(' ', '&nbsp;', trim($tmp[$i]));
            }
        }

        return (count($errors) === 0);
    }

    function isUsedInExpressions($oldName, $newName)
    {
        $count = 0;
        $names = $this->getAllTaxNames();
        foreach($names as $name){
            if($name == $oldName){
                $count++;
            }

            if($count > 1)
                return false;
        }

        $errors = array();
        $actions = $this->getActions();
        foreach($actions as $action){
            if(!$this->checkExpressionSyntax($action, $errors, $oldName)){
                return true;
            }
        }

        return false;
    }
}
// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
