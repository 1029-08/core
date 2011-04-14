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
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @see       ____file_see____
 * @since     1.0.0
 */

namespace XLite\View;

/**
 * Buy now widget
 *
 * @see   ____class_see____
 * @since 1.0.0
 */
class BuyNow extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */

    const PARAM_PRODUCT         = 'product';
    const PARAM_BUTTON_STYLE    = 'style';
    const PARAM_SHOW_PRICE      = 'showPrice';


    /**
     * Get product
     * 
     * @return \XLite\Model\Product
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getProduct()
    {
        return $this->getParam(self::PARAM_PRODUCT);
    }

    /**
     * Check whether the product price is to be shown as the button label
     * 
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function isShowPrice()
    {
        return $this->getParam(self::PARAM_SHOW_PRICE);
    }


    /**
     * Return widget default template
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getDefaultTemplate()
    {
        return 'buy_now.tpl';
    }

    /**
     * Define widget parameters
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_PRODUCT         => new \XLite\Model\WidgetParam\Object('Product', null, false, '\XLite\Model\Product'),
            self::PARAM_BUTTON_STYLE    => new \XLite\Model\WidgetParam\String('Button style', ''),
            self::PARAM_SHOW_PRICE      => new \XLite\Model\WidgetParam\String('Show the product price in the button', ''),
        );
    }
}
