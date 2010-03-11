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
* RecentlyViewed widget
*
* @package Module_ProductAdviser
* @access public
* @version $Id$
*/
class XLite_Module_ProductAdviser_View_RecentlyViewed extends XLite_View_ProductsList
{	
	/**
     * Targets this widget is allowed for
     *
     * @var    array
     * @access protected
     * @since  3.0.0
     */
    protected $allowedTargets = array('main', 'category', 'product', 'cart', 'recently_viewed');

	/**
	 * The number of products displayed by widget 
	 * 
	 * @var    integer
	 * @access public
	 * @see    ____var_see____
	 * @since  3.0.0
	 */
	public $productsNumber = 0;

	/**
	 * Flag that means if it is need to display link 'See more...'
	 * 
	 * @var    bool
	 * @access public
	 * @see    ____var_see____
	 * @since  3.0.0
	 */
	public $additionalPresent = false;

    /**
     * Recently viewed products array
     * 
     * @var    array
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $recentlyViewedProducts = null;

	/**
	 * Get widget title 
	 * 
	 * @return string
	 * @access public
	 * @see    ____func_see____
	 * @since  3.0.0
	 */
	public function getHead()
	{
		return 'Recently viewed';
	}

    /**
     * Define widget parameters
     *
     * @return void
     * @access protected
     * @since  3.0.0
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[self::PARAM_DISPLAY_MODE]->setValue(self::DISPLAY_MODE_LIST);
        $this->widgetParams[self::PARAM_GRID_COLUMNS]->setValue(3);
        $this->widgetParams[self::PARAM_SHOW_DESCR]->setValue(true);
        $this->widgetParams[self::PARAM_SHOW_PRICE]->setValue(true);
        $this->widgetParams[self::PARAM_SHOW_ADD2CART]->setValue(true);
        $this->widgetParams[self::PARAM_SIDEBAR_MAX_ITEMS]->setValue($this->config->ProductAdviser->number_recently_viewed);

        $this->widgetParams[self::PARAM_SHOW_DISPLAY_MODE_SELECTOR]->setValue(false);
        $this->widgetParams[self::PARAM_SHOW_ALL_ITEMS_PER_PAGE]->setValue(false);
        $this->widgetParams[self::PARAM_SHOW_SORT_BY_SELECTOR]->setValue(false);
        $this->widgetParams[self::PARAM_SORT_BY]->setValue('Name');
        $this->widgetParams[self::PARAM_SORT_ORDER]->setValue('asc');

        foreach ($this->getHiddenParamsList() as $param) {
            $this->widgetParams[$param]->setVisibility(false);
        }
    }

    /**
     * Get the list of parameters that are hidden on the settings page 
     * 
     * @return array
     * @access protected
     * @since  3.0.0
     */
    protected function getHiddenParamsList()
    {
        return array(
            self::PARAM_SHOW_DISPLAY_MODE_SELECTOR,
            self::PARAM_SHOW_SORT_BY_SELECTOR,
            self::PARAM_SORT_BY,
            self::PARAM_SORT_ORDER,
            self::PARAM_SHOW_ALL_ITEMS_PER_PAGE,
        );
    }

    /**
     * Check if there are product to display
     *
     * @return bool
     * @access protected
     * @since  3.0.0 EE
     */
    protected function checkProductsToDisplay()
    {
        return 0 < $this->getParam(self::PARAM_SIDEBAR_MAX_ITEMS) && $this->getRecentliesProducts();
    }

	/**
	 * Return current product Id (if presented)
     * TODO - check if it's really needed
	 * 
	 * @return mixed
	 * @access protected
	 * @since  3.0.0 EE
	 */
	protected function getDialogProductId()
    {
		return ('product' == XLite_Core_Request::getInstance()->target) ? XLite_Core_Request::getInstance()->product_id : null;
    }

	/**
     * Check if widget is visible
     *
     * @return bool
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function isVisible()
    {
        return parent::isVisible()
            && $this->checkProductsToDisplay()
            && !(self::WIDGET_TYPE_SIDEBAR == $this->getParam(self::PARAM_WIDGET_TYPE) && 'recently_viewed' == XLite_Core_Request::getInstance()->target);
    }

    /**
     * Return products list
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getData()
    {
        return $this->getRecentliesProducts();
    }

    /**
     * Check status of 'More...' link for sidebar list
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function isShowMoreLink()
    {
        return $this->additionalPresent;
    }

    /**
     * Get 'More...' link URL for sidebar list
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getMoreLinkURL()
    {
        return $this->buildURL('recently_viewed');
    }

    /**
     * Get 'More...' link text for sidebar list
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getMoreLinkText()
    {
        return 'All viewed...';
    }

    /**
     * Get recently viewed products list
     * 
     * @return array of XLite_Model_Product objects
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getRecentliesProducts()
    {
        if (!isset($this->recentlyViewedProducts)) {

    		$product_id = (int)$this->getDialogProductId();

            $rvObj = new XLite_Module_ProductAdviser_Model_ProductRecentlyViewed();
            $rvProducts = $rvObj->findAll("sid='".$this->session->getID()."' AND product_id != '$product_id'", 'last_viewed DESC');
            $products = array();

            if (is_array($rvProducts)) {

                foreach ($rvProducts as $rvProduct) {

                    $product = new XLite_Model_Product($rvProduct->get("product_id"));

                    $addSign = (isset($product_id) && $product->get("product_id") == $product_id) ? false : true;

                    if ($addSign) {
                        $addSign &= $product->filter();
                        $addSign &= $product->is("available");

                        // additional check
                        if (!$product->is("available") || (isset($product->properties) && is_array($product->properties) && !isset($product->properties["enabled"]))) {
                            // removing link to non-existing product
                            if (intval($rvProduct->get("product_id")) > 0) {
                                $rvProduct->delete();
                            }
                            $addSign = false;
                        }

                        if ($addSign) {
                            $product->checkSafetyMode();
                        	$products[] = $product;
                        }
                    }
                }
            }

            if (!empty($products)) {
                $this->recentlyViewedProducts = $products;
            }
        }

        if ('recently_viewed' != XLite_Core_Request::getInstance()->target && is_array($this->recentlyViewedProducts) && count($this->recentlyViewedProducts) > $this->getParam(self::PARAM_SIDEBAR_MAX_ITEMS)) {

            $return = array();
            $index = $this->getParam(self::PARAM_SIDEBAR_MAX_ITEMS);

            foreach ($this->recentlyViewedProducts as $product) {
                if ($index-- <= 0) {
                    break;
                }
                $return[] = $product;
            }

            $this->additionalPresent = true;

        } else {
            $return = $this->recentlyViewedProducts;
        }

        return $return;
	}
}
