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
 * @copyright Copyright (c) 2011-2012 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 */

namespace XLite\Module\CDev\ProductOptions\View;

/**
 * Product option modifier widget
 *
 */
class ProductOptionModifier extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */

    const PARAM_OPTION = 'option';


    /**
     * Get modifiers
     *
     * @return array
     */
    public function getModifiers()
    {
        return $this->getParam(self::PARAM_OPTION)->getNotEmptyModifiers();
    }

    /**
     * Get modifier personal template
     *
     * @param \XLite\Module\CDev\ProductOptions\Model\OptionSurcharge $surcharge Modifier
     *
     * @return string
     */
    public function getModifierTemplate(\XLite\Module\CDev\ProductOptions\Model\OptionSurcharge $surcharge)
    {
        return 'modules/CDev/ProductOptions/display/modifier/' . $surcharge->getType() . '.tpl';
    }


    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/ProductOptions/product_option_modifier.tpl';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_OPTION => new \XLite\Model\WidgetParam\Object(
                'Option',
                null,
                false,
                '\XLite\Module\CDev\ProductOptions\Model\Option'
            ),
        );
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getParam(self::PARAM_OPTION)->isModifier();
    }
}
