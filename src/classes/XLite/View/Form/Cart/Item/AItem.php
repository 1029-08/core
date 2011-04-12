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

namespace XLite\View\Form\Cart\Item;

/**
 * Abstract cart item form
 * 
 * @see   ____class_see____
 * @since 1.0.0
 */
abstract class AItem extends \XLite\View\Form\AForm
{
    /**
     * Widget paramater names
     */
    const PARAM_ITEM    = 'item';


    /**
     * Current form name 
     * 
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getFormName()
    {
        return 'cart_item_' . $this->getParam(self::PARAM_ITEM)->getItemId();
    }

    /**
     * getDefaultTarget
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getDefaultTarget()
    {
        return 'cart';
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

        $this->widgetParams[self::PARAM_ITEM] = new \XLite\Model\WidgetParam\Object(
            'Cart item', null, false, '\XLite\Model\OrderItem'
        );
    }

    /**
     * Called before the includeCompiledFile()
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function initView()
    {
        parent::initView();

        $this->widgetParams[self::PARAM_FORM_PARAMS]->appendValue(
            array(
                'cart_id' => $this->getParam(self::PARAM_ITEM)->getItemId()
            )
        );
    }
}
