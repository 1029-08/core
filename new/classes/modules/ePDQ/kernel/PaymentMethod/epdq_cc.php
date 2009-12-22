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
* @package Module_ePDQ
* @access public
* @version $Id$
*/
class PaymentMethod_epdq_cc extends PaymentMethod_credit_card
{
	var $configurationTemplate = "modules/ePDQ/config.tpl";
	var $formTemplate = "modules/ePDQ/checkout.tpl";
    var $hasConfigurationForm = true;
    var $processorName = "ePDQ";

    function handleRequest(&$cart)
    {
		require_once "modules/ePDQ/encoded.php";
        func_PaymentMethod_epdq_cc_handleRequest($this, $cart);
    }

	function getePDQdata(&$cart)
	{
		$merchant = $this->get("params.param01");
		$clientid = $this->get("params.param02");
		$phrase   = $this->get("params.param03");
		$currency = $this->get("params.param04");
		$auth     = $this->get("params.param05");
		$cpi_logo = $this->get("params.param06");
		$ordr = $cart->get("order_id");

#the following parameters have been obtained earlier in the merchant's webstore: clientid, passphrase, oid, currencycode, total
		$_params="clientid=" . $clientid;
		$_params.="&password=" . $phrase;
		$_params.="&oid=" . $ordr;
		$_params.="&chargetype=" . $auth;
		$_params.="&currencycode=" . $currency;
		$_params.="&total=" . $cart->get("total");

#perform the HTTP Post

		$request = func_new('HTTPS');
		$request->urlencoded = true;
		$request->url = $this->get("params.param08");
		$request->data = $_params;
		$request->request();
		return $request->response;
	}
}
// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
