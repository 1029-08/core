<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * LiteCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@litecommerce.com so we can send you a copy immediately.
 *
 * PHP version 5.3.0
 *
 * @category  LiteCommerce
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @see       ____file_see____
 * @since     1.0.0
 */

namespace XLite\Module\CDev\VAT\Logic\Product;

/**
 * Tax business logic
 *
 * @see   ____class_see____
 * @since 1.0.0
 */
class Tax extends \XLite\Logic\ALogic
{
    /**
     * Taxes (cache)
     *
     * @var   array
     * @see   ____var_see____
     * @since 1.0.13
     */
    protected $taxes;

    // {{{ Product search

    /**
     * Get search price condition 
     * 
     * @param string $priceField   Price field name (ex. 'p.price')
     * @param string $classesAlias Produyct classes table alias (ex. 'classes')
     *  
     * @return string
     * @see    ____func_see____
     * @since  1.0.8
     */
    public function getSearchPriceConbdition($priceField, $classesAlias)
    {
        $cnd = $priceField;

        foreach ($this->getTaxes() as $tax) {
            $includedZones = $tax->getVATZone() ? array($tax->getVATZone()->getZoneId()) : array();
            $included = $tax->getFilteredRate($includedZones, $tax->getVATMembership());

            if ($included) {
                $cnd .= ' - (' . $included->getExcludeTaxFormula($priceField) . ')';
            }
        }

        return $cnd;
    }

    // }}}

    // {{{ Calculation

    /**
     * Calculate product price
     * 
     * @param \XLite\Model\Product $product Product
     * @param float                $price   Price
     *  
     * @return float
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function calculateProductPrice(\XLite\Model\Product $product, $price)
    {
        return $this->deductTaxFromPrice($product, $price);
    }

    /**
     * Calculate product net price
     * 
     * @param \XLite\Model\Product $product Product
     * @param float                $price   Price
     *  
     * @return float
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function calculateProductNetPrice(\XLite\Model\Product $product, $price)
    {
        return $this->deductTaxFromPrice($product, $price);
    }


    /**
     * Calculate product-based included taxes
     * 
     * @param \XLite\Model\Product $product Product
     *  
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function calculateProduct(\XLite\Model\Product $product, $price = null)
    {
        $zones = $this->getZonesList();
        $membership = $this->getMembership();
        //$price = $this->deductTaxFromPrice($product, isset($price) ? $price : $product->getTaxableBasis());

        $taxes = array();

        foreach ($this->getTaxes() as $tax) {

            $rate = $tax->getFilteredRate($zones, $membership, $product->getClasses());

            if ($rate) {
                $taxes[$tax->getName()] = $rate->calculateProductPriceIncludingTax($product, $price);
            }
        }

        return $taxes;
    }

    /**
     * getDisplayPrice 
     * 
     * @param \XLite\Model\Product $product ____param_comment____
     * @param mixed                $price   ____param_comment____
     *  
     * @return void
     * @see    ____func_see____
     * @since  1.0.19
     */
    public function getDisplayPrice(\XLite\Model\Product $product, $price)
    {
        //$netPrice = $this->calculateProductPrice($product, $price);

        $netPrice = $price;

        if (\XLite\Core\Config::getInstance()->CDev->VAT->display_prices_including_vat) {

            $taxes = $this->calculateProduct($product, $netPrice);

            if (!empty($taxes)) {
                foreach ($taxes as $tax) {
                    $netPrice += $tax;
                }
            }
        }

        return $netPrice;
    }

    public function getVATValue(\XLite\Model\Product $product, $price)
    {
        $taxes = $this->calculateProduct($product, $price);

        $taxTotal = 0;

        if (!empty($taxes)) {
            foreach ($taxes as $tax) {
                $taxTotal += $tax;
            }
        }

        return $taxTotal;
    }

    /**
     * Calculate product net price
     * 
     * @param \XLite\Model\Product $product Product
     * @param float                $price   Price
     *  
     * @return float
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function deductTaxFromPrice(\XLite\Model\Product $product, $price)
    {
        foreach ($this->getTaxes() as $tax) {
            $includedZones = $tax->getVATZone() ? array($tax->getVATZone()->getZoneId()) : array();
            $included = $tax->getFilteredRate($includedZones, $tax->getVATMembership(), $product->getClasses());

            if ($included) {
                $price -= $included->calculateProductPriceExcludingTax($product, $price);
            }
        }

        return $price;
    }

    /**
     * Get taxes 
     * 
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getTaxes()
    {
        if (!isset($this->taxes)) {
            $this->taxes = \XLite\Core\Database::getRepo('XLite\Module\CDev\VAT\Model\Tax')->findActive();
        }

        return $this->taxes;
    }

    /**
     * Get zones list 
     * 
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getZonesList()
    {
        $address = $this->getAddress();

        $zones = $address ? \XLite\Core\Database::getRepo('XLite\Model\Zone')->findApplicableZones($address) : array();

        foreach ($zones as $i => $zone) {
            $zones[$i] = $zone->getZoneId();
        }

        return $zones;
    }

    /**
     * Get membership 
     * 
     * @return \XLite\Model\Membership
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getMembership()
    {
        return $this->getProfile()->getMembership();
    }

    /**
     * Get profile 
     * 
     * @return \XLite\Model\Profile
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getProfile()
    {
        return \XLite\Core\Auth::getInstance()->getProfile() ?: $this->getDefaultProfile();
    }

    /**
     * Get default profile if user is not authorized
     * 
     * @return \XLite\Model\Profile
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getDefaultProfile()
    {
        return new \XLite\Model\Profile;
    }

    /**
     * Get address for zone calculator
     * 
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getAddress()
    {
        $address = null;

        $addressObj = $this->getProfile()->getShippingAddress();

        if ($addressObj) {

            // Profile is exists
            $address = array(
                'address' => $addressObj->getStreet(),
                'city'    => $addressObj->getCity(),
                'state'   => $addressObj->getState()->getStateId(),
                'zipcode' => $addressObj->getZipcode(),
                'country' => $addressObj->getCountry() ? $addressObj->getCountry()->getCode() : '',
            );
        }

        if (!isset($address)) {

            // Anonymous address
            $config = \XLite\Core\Config::getInstance()->Shipping;
            $address = array(
                'address' => $config->anonymous_address,
                'city'    => $config->anonymous_city,
                'state'   => $config->anonymous_state,
                'zipcode' => $config->anonymous_zipcode,
                'country' => $config->anonymous_country,
            );
        }

        return $address;
    }

    // }}}
}
