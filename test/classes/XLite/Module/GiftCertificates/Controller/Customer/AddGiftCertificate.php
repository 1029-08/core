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

/* vim: set expandtab tabstop=4 softtabstop=4 foldmethod=marker shiftwidth=4: */

/**
* Dialog_add_gift_certificate  - an add GC form dialog.
*
* @package Module_GiftCertificates
* @access public
* @version $Id$
*/
class XLite_Module_GiftCertificates_Controller_Customer_AddGiftCertificate extends XLite_Controller_Customer_Abstract
{	
    public $params = array('target', 'gcid');	
	public $gc = null;
    
    function getGC()
    {
        if (is_null($this->gc)) {
            if ($this->get('gcid')) {
                $this->gc = new XLite_Module_GiftCertificates_Model_GiftCertificate($this->get('gcid'));

            } else {

                // set default form values
                $this->gc = new XLite_Module_GiftCertificates_Model_GiftCertificate();

                $this->gc->set("send_via", "E");
                $this->gc->set("border", "no_border");
                $auth = XLite_Model_Auth::getInstance();
                if ($auth->isLogged()) {
                    $profile = $auth->get("profile");
                    $this->gc->set("purchaser", $profile->get("billing_title") . " " . $profile->get("billing_firstname") . " " . $profile->get("billing_lastname"));
                }
                $this->gc->set("recipient_country", $this->config->General->default_country);
            }
        }

        return $this->gc;
    }

	function fillForm()
	{
        $this->set("properties", $this->getGC()->get('properties'));
    }

    function isGCAdded()
    {
        if (is_null($this->getGC()) || !$this->getGC()->isPersistent) {
            return false;
		}

        $items = $this->cart->get("items");
        $found = false;
        for ($i = 0; $i < count($items); $i++) {
            if ($items[$i]->get('gcid') == $this->getGC()->get('gcid')) {
                $found = true;
                break;
            }
        }

        return $found;
    }

	function action_add()
	{
        $this->saveGC();

        $found = false;
		$items = $this->cart->get("items");

		for ($i = 0; $i < count($items); $i++) {
			if ($items[$i]->get('gcid') == $this->getGC()->get('gcid')) {
				$items[$i]->set("GC", $this->getGC());
				$items[$i]->update();
                $found = true;
			}
		}

        if (!$found) {
			$oi = new XLite_Model_OrderItem();
			$oi->set("GC", $this->getGC());
			$this->cart->addItem($oi);
    	}

		if ($this->cart->isPersistent) {
			$this->cart->calcTotals();
			$this->cart->update();
    		$items = $this->cart->get("items");
    		for ($i = 0; $i < count($items); $i++) {
    			if ($items[$i]->get('gcid') == $this->getGC()->get('gcid')) {
    				$this->cart->updateItem($items[$i]);
    			}
    		}
		}

        $this->set("returnUrl", $this->buildURL('cart'));
	}

    function action_select_ecard()
    {
        $this->saveGC();
        $this->set('returnUrl', $this->buildURL('gift_certificate_ecards', '', array('gcid' => $this->getGC()->get('gcid'))));
    }

    function action_delete_ecard()
    {
        $this->saveGC();
		if (!is_null($this->getGC())) {
			$gc = $this->getGC();
            $gc->set("ecard_id", 0);
            $gc->update();
            $this->set("returnUrl", $this->buildURL('add_gift_certificate', '', array('gcid' => $gc->get('gcid'))));
        }
    }

    function action_preview_ecard()
    {
        $this->saveGC();
        $this->set("returnUrl", $this->buildURL('preview_ecard', '', array('gcid' => $this->getGC()->get('gcid'))));
    }

    function saveGC()
    {
        if (isset($this->border)) {
            $this->border = str_replace(array('.', '/'), array('', ''), $this->border);
        }

		if (!is_null($this->getGC())) {
			$gc = $this->getGC();
    		$gc->setProperties(XLite_Core_Request::getInstance()->getData());
    		$gc->set("status", "D");
    		$gc->set("debit", $gc->get("amount"));
    		$gc->set("add_date", time());
			if (!$gc->get("expiration_date")) {
				$month = 30 * 24 * 3600;
				$gc->set("expiration_date", time() + $month * $gc->get('defaultExpirationPeriod'));
			}

        	if ($gc->get('gcid')) {
                $gc->update();

            } else {
                $gc->set('gcid', $gc->generateGC());
				$gc->set("profile_id", $this->xlite->auth->getComplex('profile.profile_id'));
                $gc->create();
            }
        }
    }
    
    function getCountriesStates() {
        $countriesArray = array();

        $country = new XLite_Model_Country();
        $countries = $country->findAll("enabled = '1'");
        foreach($countries as $country) {
            $countriesArray[$country->get("code")]["number"] = 0;
            $countriesArray[$country->get("code")]["data"] = array();

            $state = new XLite_Model_State();
            $states = $state->findAll("country_code = '".$country->get("code")."'");
            if (is_array($states) && count($states) > 0) {
                $countriesArray[$country->get("code")]["number"] = count($states);
                foreach($states as $state) {
                    $countriesArray[$country->get("code")]["data"][$state->get("state_id")] = $state->get("state");
                }
            }
        }

        return $countriesArray;
    }
    
    /**
     * Get page instance data (name and URL)
     * 
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getPageInstanceData()
    {
        $this->target = 'add_gift_certificate';

        return parent::getPageInstanceData();
    }

    /**
     * Get page type name
     * 
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getPageTypeName()
    {
        return 'Add gift certificate';
    }

}

