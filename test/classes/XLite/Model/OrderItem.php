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
 * @subpackage ____sub_package____
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

/**
 * Something customer can put into its cart
 * 
 * @package    XLite
 * @subpackage ____sub_package____
 * @since      3.0.0
 */
class XLite_Model_OrderItem extends XLite_Model_Abstract
{
	/**
	 * Return reference to the associated order object
	 * 
	 * @return XLite_Model_Order
	 * @access public
	 * @since  3.0.0 EE
	 */
	public function getOrder()
	{
		return XLite_Model_CachingFactory::getObject(__METHOD__ . $this->_uniqueKey, 'XLite_Model_Order', array($this->get('order_id')));
	}

	/**
	 * A reference to the product object 
	 * 
	 * @return XLite_Model_Product
	 * @access public
	 * @since  3.0.0
	 */
	public function getProduct()
    {
		return XLite_Model_CachingFactory::getObject(__METHOD__ . $this->_uniqueKey, 'XLite_Model_Product', array($this->get('product_id')));
    }

    /**
     * Flag; is this item needs to be shipped
     * 
     * @return bool
     * @access public
     * @since  3.0.0
     */
    public function isShipped()
    {
        return !$this->getProduct()->is('free_shipping');
    }



    public $fields = array(
        'order_id'    => '',
        'item_id'     => '',
        'orderby'     => 0,
        'product_id'  => '',
        'product_name'  => '',
        'product_sku'  => '',
        'price'       => '0',
        'amount'      => '1');	

    public $primaryKey = array('order_id', 'item_id');	
    public $alias = 'order_items';	
    public $defaultOrder = "orderby";

	public function __construct()
	{
		$this->_uniqueKey = uniqid("order_item_");
		parent::__construct();
	}

    public function setProduct($product)
    {
        $this->product = $product;

        if (is_null($product)) {
            $this->set("product_id", 0);

        } else {
        	if ($this->config->Taxes->prices_include_tax) {
        		$this->set("price", $this->formatCurrency($product->get("taxedPrice")));
        	} else {
            	$this->set("price", $product->get("price"));
        	}

            $this->set("product_id", $product->get("product_id"));
            $this->set("product_name", $product->get("name"));
            $this->set("product_sku", $product->get("sku"));
        }
    }

    function create()
    {
        $this->set("item_id", $this->get("key"));
        parent::create();
    }
    
    /**
    * Returns a scalar key value used to identify items in shopping cart
    */
    function getKey()
    {
        return $this->get("product_id"); // . product_options
    }

    function updateAmount($amount)
    {
        $amount = (int)$amount;
        if ($amount <= 0) {
            $this->getOrder()->deleteItem($this);
        } else {
            $this->set("amount", $amount);
            $this->update();
        }
    }

    function getOrderby()
    {
        $sql = "SELECT MAX(orderby)+1 FROM %s WHERE order_id=%d";
        $sql = sprintf($sql, $this->get("table"), $this->get("order_id"));
        return $this->db->getOne($sql);
    }

    function getDiscountablePrice()
    {
        return $this->get("price");
    }

    function getTaxableTotal()
    {
        return $this->get("total");
    }

    function getPrice()
    {
        return $this->formatCurrency($this->get("price"));
    }

    function getTotal()
    {
        return $this->formatCurrency($this->get("price") * $this->get("amount"));
    }

    function getWeight()
    {
        return $this->getComplex('product.weight') * $this->get("amount");
    }

    function getRealProduct()
    {
		$this->realProduct = null;
    	$product = new XLite_Model_Product();
        $product->find("product_id='".$this->get("product_id")."'");
    	if ($product->get("product_id") == $this->get("product_id")) {
    		$this->realProduct = $product;
			return true;
		}
		return false;
    }

    function get($name)
    {
		if ($name == 'name' || $name == 'brief_description' || 
            $name == 'description' || $name == 'sku') {
        	    $product = $this->get("product");
        	    if (is_object($product) && $this->getRealProduct() && (!isset($product->properties["$name"]) || !$this->realProduct->get("enabled"))) {
					return $this->realProduct->get("$name");
                } else {
                    if ($name == "name" || $name == "sku")
                        return $this->get("product_$name");
                }
            return $this->getProduct()->get($name);
        }
        return parent::get($name);
    }
	
	function hasThumbnail()
	{
		return (!$this->isValid() && $this->getRealProduct()) ? $this->realProduct->hasThumbnail() : $this->getProduct()->hasThumbnail();
	}

	function getThumbnailURL()
	{
		return (!$this->isValid() && $this->getRealProduct()) ? $this->realProduct->getThumbnailURL() : $this->getProduct()->getThumbnailURL();
	}
    
	/**
	* This method is used in payment methods to briefly describe
	* the identity of the item.
	*/
    function getDescription()
    {
        return $this->get("name").' ('.$this->get("amount").')';
    }

    function getShortDescription($limit = 30)
    {
        if (strlen($this->get("sku"))) {
            $desc = $this->get("sku");
        } else {
            $desc = substr($this->get("name"), 0, $limit);
        }
        if ($this->get("amount") == 1) {
            return $desc;
        } else {
            return $desc . ' (' . $this->get("amount") . ')';
        }
    }

	/**
	* Validates the order item (e.g. the product_id supplied is an existing
	* product id, the amount is greater than zero etc.).
	* You cannot add an invalid item to a cart (prevented in Order::addItem()).
	* This procedure disabled possible work-arounds of standard dialog 
	* restrictions and is not intended to, say, restrict product options
	* and other cases when the cart must show an error/explanation message
	* to customer.
	*/
	function isValid()
	{
	    $product = $this->get("product");
	    if (is_object($product)) {
			$res = $this->isComplex('product.exists') && $this->getComplex('product.product_id') && $this->get("amount")>0;
		} else {
			$res = $this->get("amount")>0;
		}
        return $res;
	}

    /**
    * Decide whether to use shopping_cart/item.tpl widget to display
    * this item. Must be false if you want to use an alternative template.
    */
    function isUseStandardTemplate()
    {
        return true;
    }

    /**
    * Returns the item descriptiopn URL in the shopping cart. FIXME
    */
    function getURL()
    {
		$params = array('product_id' => $this->get('product_id'));

        $category_id = $this->getProduct()->getCategory()->get('category_id');
        if ($category_id) {
            $params['category_id'] = $category_id;
        }

		return XLite_Core_Converter::getInstance()->buildURL('product', '', $params);
    }

	public function hasOptions()
	{
		return false;
	}
}

