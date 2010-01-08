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

/**
* Class description.
*
* @package Module_UPSOnlineTools
* @access public
* @version $Id$
*
*/

class XLite_Module_UPSOnlineTools_base_Dialog extends XLite_Controller_Abstract implements XLite_Base_IDecorator
{
    function isShowAV()
    {
        $target = $this->get('target');
        $mode = $this->get('mode');
        if ($target == 'profile'|| ($target == 'checkout' && $mode == 'register')) {
            $av_result = $this->session->get('ups_av_result');
            if (count($av_result) > 0 || $this->session->get('ups_av_error')) return true;
        }
        else {
			$this->session->set('ups_av_result', null);
			$this->session->set('ups_av_error', null);
		}

        return false;
    }

    function getUpsUsed() 
    {
        if (!isset($this->_ups_profile)) {
            $this->_ups_profile = new XLite_Model_Profile();
            $this->_ups_profile->set('properties', $this->session->get('ups_used'));
        }

        return $this->_ups_profile;
    }

	function isSuggestionExists()
	{
		$av_result = $this->session->get('ups_av_result');
		return (count($av_result) > 0) ? true : false;
	}

	function isAVError()
	{
		return $this->session->get('ups_av_error');
	}

	function getAVErrorMessage()
	{
		$errcode = $this->session->get('ups_av_errorcode');
		if (empty($errcode)) {
			return "Unable to connect. UPS OnLine&reg; Tools Address Validation service is not available.";
		} else {
			return "UPS OnLine&reg; Tools Address Validation returned an error: (".$errcode.") ".$this->session->get('ups_av_errordescr');
		}
	}

}

// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
