<?php
/*
+------------------------------------------------------------------------------+
| LiteCommerce                                                                 |
| Copyright (c) 2003-2009 Creative Development <info@creativedevelopment.biz>  |
| All rights reserved.                                                         |
+------------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE  "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION.  THE AGREEMENT TEXT  IS ALSO AVAILABLE |
| AT THE FOLLOWING URL: http://www.litecommerce.com/license.php                |
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
| The Initial Developer of the Original Code is Ruslan R. Fazliev              |
| Portions created by Ruslan R. Fazliev are Copyright (C) 2003 Creative        |
| Development. All Rights Reserved.                                            |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */

/**
* .
*
* @package 
* @access
* @version $Id$
*/

class XLite_Module_AntiFraud_Controller_Admin_Order extends XLite_Controller_Admin_Order implements XLite_Base_IDecorator
{	
	public $order = null;	
	public $country = null;
	
	function init() 
	{
		$this->params[] = "mode";
		parent::init();
	}

	function action_fraud_notify()
	{
		$post = array();
		$post["mode"] = "add_ip";
		$post["ip"]	= $this->get("order.address");
		$post["shop_host"] = func_parse_host(XLite::getInstance()->getOptions(array('host_details', 'http_host')));
		$post["reason"] = strip_tags($this->get("fraud_comment"));
		$post["service_key"] = $this->config->get("AntiFraud.antifraud_license");
		$request = new XLite_Model_HTTPS();
        $request->data = $post; 
        $request->url = $this->config->get('AntiFraud.antifraud_url')."/add_fraudulent_ip.php";
		$request->request();

		$request->response ? $this->set("mode","sent") : $this->set("mode","failed");
	}

	function getOrder()
	{
		if (is_null($this->order)) {
			$this->order = new XLite_Model_Order($this->get("order_id"));
		} 
		return $this->order;
	}	

	function compare($val1, $val2) 
	{
		return ($val1 >= $val2) ? 1 : 0; 	
	}
	
    function getCountry()
    {
        if (!is_null($this->country)) {
            return $this->country;
        }   

        $order = $this->get("order");
        $this->country = new XLite_Model_Country($order->get("profile.billing_country"));
        $this->country->set("order", $order);
        return $this->country;
    }

	function action_check_fraud() 
	{
		$order = $this->get("order");
		$order->checkFraud();
		$order->update();
	}
}

// WARNING:
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
