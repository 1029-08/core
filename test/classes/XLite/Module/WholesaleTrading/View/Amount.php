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
 * @category   LiteCommerce
 * @package    XLite
 * @subpackage View
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

/**
 * Product amount widget
 *
 * @package    XLite
 * @subpackage View
 * @since      3.0
 */
class XLite_Module_WholesaleTrading_View_Amount extends XLite_View_Abstract
{
    /**
     * Widget parameter names
     */

    const PARAM_PRODUCT = 'product';


    /**
     * Define widget parameters
     *
     * @return void
     * @access protected
     * @since  1.0.0
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_PRODUCT => new XLite_Model_WidgetParam_Object('Product', null, false, 'XLite_Model_Product'),
        );

        $this->widgetParams[self::PARAM_TEMPLATE]->setValue('modules/WholesaleTrading/amount.tpl');
    }

    /**
     * Check visibility 
     * 
     * @return boolean
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function isVisible()
    {
        return parent::isVisible()
            && $this->getParam(self::PARAM_PRODUCT)->isPriceAvailable()
            && $this->isAvailableForSale();
    }

    /**
     * Register JS files
     *
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/WholesaleTrading/amount.js';
        $list[] = 'modules/WholesaleTrading/jquery.mousewheel.js';

        return $list;
    }

    /**
     * Get maximum quantity 
     * 
     * @return integer
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getMaxAmount()
    {
        $purchaseLimit = $this->getParam(self::PARAM_PRODUCT)->get('purchaseLimit');
        $inventory = $this->getParam(self::PARAM_PRODUCT)->get('inventory');

        $purchaseLimit = ($purchaseLimit && $purchaseLimit->get('max') > 0) ? $purchaseLimit->get('max') : 0;
        $amount = ($inventory && $inventory->get('amount') > 0) ? $inventory->get('amount') : 0;
        if ($amount > $purchaseLimit && $purchaseLimit > 0) {
            $amount = $purchaseLimit;
        }

        return $amount;
    }

    /**
     * Get minimum quantity 
     * 
     * @return integer
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getMinAmount()
    {
        $purchaseLimit = $this->getParam(self::PARAM_PRODUCT)->get('purchaseLimit');

        return ($purchaseLimit && $purchaseLimit->get('min') > 1) ? $purchaseLimit->get('min') : 1;
    }

}

