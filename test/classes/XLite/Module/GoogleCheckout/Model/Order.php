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
| GRANTED  BY  THIS AGREEMENT.                                                 |
|                                                                              |
| The Initial Developer of the Original Code is Ruslan R. Fazliev              |
| Portions created by Ruslan R. Fazliev are Copyright (C) 2003 Creative        |
| Development. All Rights Reserved.                                            |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */

/**
* @package GoogleCheckout
* @access public
* @version $Id$
*/
class XLite_Module_GoogleCheckout_Model_Order extends XLite_Model_Order
{
	var $GoogleCheckout_profile = null;

	function constructor($id=null)
	{
		parent::constructor($id);
		$this->fields["google_id"] = '';
		$this->fields["google_total"] = 0;
		$this->fields["google_details"] = "";
		$this->fields["google_status"] = '';	// '_empty_' - not set, 'P' - partial refund, R - full refund, 'C' - canceled
		$this->fields["google_carrier"] = "";
	}

	function getGoogleCheckoutXML($name)
	{
		$methodName = "getGoogleCheckoutXML_" . strtoupper(substr($name, 0, 1)) . strtolower(substr($name, 1));
        if (method_exists($this, $methodName)) {
			$params = func_get_args();
			array_shift($params);
			return call_user_func_array(array(&$this, $methodName), $params);
        }

		return "";
	}

	function getGoogleCheckoutXML_Items()
	{
		$itemsXML = array();
        $items = $this->getItems();
        foreach ($items as $item) {
        	$itemsXML[] = $item->getGoogleCheckoutXML();
        }

		$currency = $this->xlite->get("gcheckout_currency");

		// Send Global discount as order item with negative price
		if ($this->xlite->get("WholesaleTradingEnabled") && $this->get("global_discount") > 0) {
			$discountTotal = - $this->get("global_discount");
			$itemDescription = "Shopping cart global discount.";

			$itemsXML[] = <<<EOT
		<item>
			<item-name>Global discount</item-name>
			<item-description>$itemDescription</item-description>
			<unit-price currency="$currency">$discountTotal</unit-price>
			<quantity>1</quantity>
		</item>
EOT;
		}

		// Allow to use discounts as cart items
		if (!$this->xlite->get("gcheckout_remove_discounts")) {
			// Send Discount Coupon as cart item with negative price
			if ($this->xlite->get("PromotionEnabled") && $this->getDC()) {
				$coupon = $this->getDC();

				include_once "modules/GoogleCheckout/encoded.php";

				$itemName = "Discount coupon #".$coupon->get("coupon");
				$itemDescription = GoogleCheckout_getCouponApplyDescription($coupon);
				$unitPrice = sprintf("%.02f", -doubleval($this->get("discount")));

				$itemsXML[] = <<<EOT
		<item>
			<item-name>$itemName</item-name>
			<item-description>$itemDescription</item-description>
			<unit-price currency="$currency">$unitPrice</unit-price>
			<quantity>1</quantity>
		</item>
EOT;
			}

			// Send Gift Certificate Payment as order item with negative price
			if ($this->xlite->get("GiftCertificatesEnabled") && $this->get("payedByGC") > 0) {
				$discountValue = - $this->get("payedByGC");
				$itemName = "Gift Certificate Payment";
				$itemDescription = "The order is paid for with a gift certificate partially or completely.";

				$itemsXML[] = <<<EOT
		<item>
			<item-name>$itemName</item-name>
			<item-description>$itemDescription</item-description>
			<unit-price currency="$currency">$discountValue</unit-price>
			<quantity>1</quantity>
		</item>
EOT;
			}

			// Send Payed by points Payment as order item with negative price
			if ($this->xlite->get("PromotionEnabled") && $this->get("payedByPoints") > 0) {
				$discountValue = - $this->get("payedByPoints");
				$itemName = "Bonus Points Payment";
				$itemDescription = "The order is paid for with bonus points partially or completely.";

				$itemsXML[] = <<<EOT
		<item>
			<item-name>$itemName</item-name>
			<item-description>$itemDescription</item-description>
			<unit-price currency="$currency">$discountValue</unit-price>
			<quantity>1</quantity>
		</item>
EOT;
			}
		}

		return implode("\n", $itemsXML);
	}

	function getGoogleCheckoutXML_Shippings()
	{
		$shippings = array();
		$so = new XLite_Model_Shipping();
		foreach ($so->get("modules") as $module) {
			$shipping_class = $module->get("class");

			$shipping = new XLite_Model_Shipping();
			$shippings = array_merge($shippings, $shipping->findAll("enabled=1 AND class='$shipping_class'"));
		}

		if (!is_array($shippings) || count($shippings) <= 0) {
			return "";
		}

		$shippingsXML = array();

        foreach ($shippings as $shipping) {
        	$shippingRate = new XLite_Model_ShippingRate();
        	$shippingRate->set("shipping", $shipping);
    		$shippingsXML[] = $shippingRate->getGoogleCheckoutXML();
        }

    	$shippingsXML = implode("\n", $shippingsXML);

		return <<<EOT
            <shipping-methods>
$shippingsXML
            </shipping-methods>
EOT;
	}

	function getGoogleCheckoutXML_Tax()
	{
		$subTotal = $this->calcSubTotal();
		if ($subTotal != 0) {
			$tax = $this->calcTax();
			$percent = $this->formatCurrency((($tax * 100) / $subTotal));
			$rate = $percent / 100;
		} else {
			$rate = 0;
		}

		return <<<EOT
            <tax-tables merchant-calculated="true">
                <default-tax-table>
                    <tax-rules>
                        <default-tax-rule>
                        <shipping-taxed>true</shipping-taxed>
                        <rate>$rate</rate>
                        <tax-area>
                            <us-country-area country-area="ALL"/>
                        </tax-area>
                    </default-tax-rule>
                    </tax-rules>
                </default-tax-table>
            </tax-tables>
EOT;
	}


	function getGoogleCheckoutXML_Calculation($address, $shipping, $discounts)
	{
		include_once "modules/GoogleCheckout/encoded.php";
		return GoogleCheckout_getGoogleCheckoutXML_Calculation($this, $address, $shipping, $discounts);
	}


	function getProfile()
	{
		if (is_null($this->GoogleCheckout_profile)) {
			return parent::getProfile();
		}

		return $this->GoogleCheckout_profile;
	}

	function google_checkout_setDC($dc)
	{
		if (!$this->xlite->get("PromotionEnabled"))
			return false;

		// unset existing discount coupon
		if (!is_null($this->get("DC"))) {
			$this->DC->delete();
			$this->DC = null;
		}

		if (!is_null($dc)) {
			$coupon = new XLite_Module_Promotion_Model_DiscountCoupon();
			if ( function_exists("func_is_clone_deprecated") && func_is_clone_deprecated() ) {
				$clone = $dc->cloneObject();
			} else {
				$clone = $dc->clone();
			}

			$clone->set("order_id", $this->get("order_id"));
			$clone->update();
			$this->set("discountCoupon", $dc->get("coupon_id"));
			$this->DC = $clone;
		} else {
			$this->set("discountCoupon", "");
			return false;
		}

		return true;
	}

	function isGoogleAllowPay()
	{
		foreach ($this->get("items") as $item) {
			if ($item->get("product.google_disabled"))
				return false;
		}

		return true;
	}

	function google_getItemsFingerprint()
	{
		if ($this->isEmpty()) {
			return false;
		}

		$result = array();
		$items = $this->get("items");
		foreach ($items as $item_idx => $item) {
			$result[] = array
			(
				$item_idx,
				$item->get("key"),
				$item->get("amount")
			);
		}

		return serialize($result);
	}

	function googleDisableNotification($status)
	{
		if ($this->get("payment_method") != "google_checkout") {
			return;
		}

		$disableCustomerNotif = $this->xlite->get("GoogleCheckoutDCN");
		if (!isset($disableCustomerNotif)) {
    		$pmGC = new XLite_Model_PaymentMethod("google_checkout");
    		$disableCustomerNotif = $pmGC->get("params.disable_customer_notif");
            $this->xlite->set("GoogleCheckoutDCN", $disableCustomerNotif);
		}

		if ($disableCustomerNotif) {
            $this->xlite->set("GoogleCheckoutDCNMailer", $status);
		}
	}

	function getGoogleShippingCarrirer()
	{
		if ($this->get("google_carrier"))
			return $this->get("google_carrier");

		$sm = $this->get("shippingMethod");
		switch ($sm->get("class")) {
			case "ups":
				return "UPS";

			case "usps":
				return "USPS";

			default:
				return "Other";
		}
	}

	function getGoogleRemainRefund()
	{
		return max(0, $this->get("google_details.total_charge_amount") - $this->get("google_details.refund_amount"));
	}

	function getGoogleRemainCharge()
	{
		return max(0, $this->get("google_total") - $this->get("google_details.total_charge_amount"));
	}

	function setGoogleDetails($value)
	{
		parent::set("google_details", serialize((array)$value));
	}

	function getGoogleDetails()
	{
		$details = parent::get("google_details");
		if ($details) {
			return unserialize($details);
		} else {
			return array();
		}
	}

	function get($name)
	{
		if ($name == "google_details") {
			return $this->getGoogleDetails();
		} else {
			return parent::get($name);
		}
	}

	function set($name, $value)
	{
		if ($name == "google_details") {
			$this->setGoogleDetails($value);
		} else {
			parent::set($name, $value);
		}
	}

	function isGoogleDiscountCouponsAvailable()
	{
		if ($this->xlite->get("PromotionEnabled") && ($this->config->get("Promotion.allowDC"))) {
			if (!is_null($this->getDC())) {
				return false;
			}

			$dc = new XLite_Module_Promotion_Model_DiscountCoupon();
			if ($dc->count("status='A' AND expire>='".time()."' AND order_id='0'") > 0) {
				return true;
			}
		}

		return false;
	}

	function isGoogleMeetDiscount()
	{
		if (!$this->xlite->get("PromotionEnabled")) {
			return true;
		}

		$dc = $this->getDC();
		if (!$dc || $dc->get("applyTo") == "total" || $dc->get("type") == "freeship") {
			return true;
		}

		return false;
	}

	function isGoogleGiftCertificatesAvailable()
	{
		if ($this->xlite->get("GiftCertificatesEnabled")) {
			if (!is_null($this->getGC())) {
				return false;
			}

			$gc = new XLite_Module_GiftCertificates_Model_GiftCertificate();
			$certs = $gc->findAll();
			foreach ($certs as $cert) {
				if ($cert->validate() == GC_OK && $cert->get("debit") > 0) {
					return true;
				}
			}
		}

		return false;
	}

	function isShowGoogleCheckoutNotes()
	{
		if ($this->xlite->get("gcheckout_remove_discounts")) {
			if ($this->isShowRemoveDiscountsNote())
				return true;
		}

		return $this->IsShowNotValidDiscountNote();
	}

	function isShowRemoveDiscountsNote()
	{
		if (!$this->xlite->get("gcheckout_remove_discounts")) {
			return false;
		}

		if ($this->xlite->get("PromotionEnabled")) {
			if (!is_null($this->getDC())) {
				return true;
			}

			if ($this->get("payedByPoints") > 0) {
				return true;
			}
		}

		if ($this->xlite->get("GiftCertificatesEnabled")) {
			if (!is_null($this->getGC())) {
				return true;
			}
		}

		return false;
	}

	function IsShowNotValidDiscountNote()
	{
		if ($this->xlite->get("gcheckout_remove_discounts")) {
			if ($this->isShowRemoveDiscountsNote())
				return false;
		}

		return (!$this->is("googleMeetDiscount"));
	}

	function update()
	{
		$this->googleDisableNotification(true);
		$result = parent::update();
		$this->googleDisableNotification(false);

		return $result;
	}

}

// WARNING:
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
