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
 * @subpackage Controller
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

/**
 * Change options from cart / wishlist item
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class XLite_Module_ProductOptions_Controller_Customer_ChangeOptions extends XLite_Controller_Customer_Abstract
{	
    /**
     * Item (cache)
     * 
     * @var    XLite_Model_OrderItem
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $item = null;

	/**
     * Common method to determine current location 
     * 
     * @return array 
     * @access protected 
     * @since  3.0.0 EE
     */      
    protected function getLocation()
    {
        return 'Change options';
    }

    /**
     * Initialize controller
     *
     * @return void
     * @access public
     * @since  3.0.0
     */
    public function init()
    {
        parent::init();

        $this->assembleReturnUrl();

        if (!$this->getItem()) {
            $this->redirect();
        }
    }

    /**
     * Assemble return url 
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function assembleReturnUrl()
    {
        $this->set('returnUrl', $this->buildUrl(XLite::TARGET_DEFAULT));
        if (XLite_Core_Request::getInstance()->source == 'cart') {
            $this->set('returnUrl', $this->buildUrl('cart'));
        }
    }

	/**
	 * notify_product action
	 * 
	 * @return void
	 * @access protected
	 * @see    ____func_see____
	 * @since  3.0.0
	 */
	protected function action_change()
	{
        if (XLite_Core_Request::getInstance()->source == 'cart') {
            $this->getItem()->setProductOptions(XLite_Core_Request::getInstance()->product_options);

            $invalidOptions = $this->getItem()->get('invalidOptions');
            if (is_null($invalidOptions)) {
                $this->updateCart();
                $this->set('returnUrl', $this->buildUrl('cart'));

            } else {

                // TODO - add top message
                $this->set('valid', false);
            }
        }
	}

    /**
     * Get cart / wishlist item 
     * 
     * @return XLite_Model_OrderItem
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getItem()
    {
        if (is_null($this->item)) {
            $this->item = false;

            if (
                XLite_Core_Request::getInstance()->source == 'cart'
                && is_numeric(XLite_Core_Request::getInstance()->item_id)
            ) {
                $items = $this->cart->getItems();

                $itemId = XLite_Core_Request::getInstance()->item_id;
                if (
                    isset($items[$itemId])
                    && $items[$itemId]->getProduct()
                    && $items[$itemId]->hasOptions()
                ) {
                    $this->item = $items[XLite_Core_Request::getInstance()->item_id];
                }
            }
        }

        return $this->item;
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
        $this->target = 'change_options';

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
        return 'Change options';
    }
}
