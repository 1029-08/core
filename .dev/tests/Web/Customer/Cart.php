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
 * @package    Tests
 * @subpackage Web
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

require_once __DIR__ . '/ACustomer.php';

/**
 * XLite_Web_Customer_Cart 
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class XLite_Web_Customer_Cart extends XLite_Web_Customer_ACustomer
{
    /**
     * PHPUnit default function.
     * Redefine this method only if you really need to do so.
     * In any other cases redefine the getRequest() one
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function addToCart()
    {
        $product = \XLite\Core\Database::getRepo('XLite\Model\Product')
            ->createQueryBuilder()
            ->innerJoin('p.images', 'd')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        $this->skipCoverage();

        $this->openAndWait('store/product//product_id-' . $product->getProductId());

        $this->click("//button[@class='bright add2cart']");

        $this->waitForLocalCondition(
            'jQuery(".product-details .product-buttons-added .buy-more").length > 0',
            10000,
            'check content reloading'
        );

        $this->openAndWait('store/cart');

        return $product;
    }

    public function testStructure()
    {
        $product = $this->addToCart();

        $mdash = html_entity_decode('&#8212;', ENT_NOQUOTES, 'UTF-8');
        $this->assertElementPresent(
            "//h1[@id='page-title' and text()='Your shopping bag " . $mdash . " 1 items']",
            'check page title'
        );

        $cnt = $this->getXpathCount(
            "//div[@id='cart']"
            . "/div[@id='shopping-cart']"
            . "/table[@class='selected-products']"
            . "/tbody"
            . "/tr"
            . "/th"
        );
        $this->assertEquals(4, $cnt, 'check headers count');

        $names = array('Products in bag', 'Price', 'Qty.', 'Subtotal',);
        foreach ($names as $name) {
            $this->assertElementPresent(
                "//div[@id='cart']"
                . "/div[@id='shopping-cart']"
                . "/table[@class='selected-products']"
                . "/tbody"
                . "/tr"
                . "/th[text()='$name']",
                'check ' . $name . ' header'
            );
        }

        $cnt = $this->getXpathCount(
            "//div[@id='cart']"
            . "/div[@id='shopping-cart']"
            . "/table[@class='selected-products']"
            . "/tbody"
            . "/tr[@class='selected-product']"
            . "/td"
        );
        $this->assertEquals(8, $cnt, 'check cells count');

        $cnt = $this->getXpathCount(
            "//div[@id='cart']"
            . "/div[@id='shopping-cart']"
            . "/table[@class='selected-products']"
            . "/tbody"
            . "/tr[@class='selected-product']"
        );
        $this->assertEquals(1, $cnt, 'check rows count');

        $this->assertElementPresent(
            "//div[@id='cart']"
            . "/div[@id='shopping-cart']"
            . "/table[@class='selected-products']"
            . "/tbody"
            . "/tr"
            . "/td[@class='item-remove delete-from-list']"
            . "/form[@method='post']"
            . "/div"
            . "/input[@type='image' and @class='remove' and @alt='Delete item']",
            'check remove item button'
        );

        $this->assertElementPresent(
            "//div[@id='cart']"
            . "/div[@id='shopping-cart']"
            . "/table[@class='selected-products']"
            . "/tbody"
            . "/tr"
            . "/td[@class='item-thumbnail']"
            . "/a"
            . "/img[@alt='" . $product->getName() . "']",
            'check thumbnail'
        );

        $this->assertElementPresent(
            "//div[@id='cart']"
            . "/div[@id='shopping-cart']"
            . "/table[@class='selected-products']"
            . "/tbody"
            . "/tr"
            . "/td[@class='item-info']"
            . "/p[@class='item-title']"
            . "/a[text()='" . $product->getName() . "']",
            'check item title'
        );

        $this->assertElementPresent(
            "//div[@id='cart']"
            . "/div[@id='shopping-cart']"
            . "/table[@class='selected-products']"
            . "/tbody"
            . "/tr"
            . "/td[@class='item-info']"
            . "/div[@class='item-options']"
            . "/ul[@class='selected-options']"
            . "/li"
            . "/span",
            'check item options'
        );

        $this->assertElementPresent(
            "//div[@id='cart']"
            . "/div[@id='shopping-cart']"
            . "/table[@class='selected-products']"
            . "/tbody"
            . "/tr"
            . "/td[@class='item-info']"
            . "/p[@class='item-weight']"
            . "/span[text()='Weight:']",
            'check item weight'
        );

        $this->assertElementPresent(
            "//div[@id='cart']"
            . "/div[@id='shopping-cart']"
            . "/table[@class='selected-products']"
            . "/tbody"
            . "/tr"
            . "/td[@class='item-price' and text()='$" . $product->getPrice() . "']",
            'check item price'
        );

        $multi = html_entity_decode('&#215;', ENT_NOQUOTES, 'UTF-8');
        $this->assertElementPresent(
            "//div[@id='cart']"
            . "/div[@id='shopping-cart']"
            . "/table[@class='selected-products']"
            . "/tbody"
            . "/tr"
            . "/td[@class='item-multi' and text()='$multi']",
            'check item multiplier symbol'
        );

        $this->assertElementPresent(
            "//div[@id='cart']"
            . "/div[@id='shopping-cart']"
            . "/table[@class='selected-products']"
            . "/tbody"
            . "/tr"
            . "/td[@class='item-qty']"
            . "/form[@method='post']"
            . "/div"
            . "/input[@type='text' and @value='1' and @name='amount']",
            'check item quantity'
        );

        $this->assertElementPresent(
            "//div[@id='cart']"
            . "/div[@id='shopping-cart']"
            . "/table[@class='selected-products']"
            . "/tbody"
            . "/tr"
            . "/td[@class='item-equal' and text()='=']",
            'check item equal symbol'
        );

        $this->assertElementPresent(
            "//div[@id='cart']"
            . "/div[@id='shopping-cart']"
            . "/table[@class='selected-products']"
            . "/tbody"
            . "/tr"
            . "/td[@class='item-subtotal' and contains(text(),'$" . $product->getPrice() . "')]",
            'check item subtotal'
        );

        $this->assertElementPresent(
            "//div[@id='cart']"
            . "/div[@id='shopping-cart']"
            . "/div[@class='cart-buttons']"
            . "/button[@class='action']"
            . "/span[text()='Continue shopping']",
            'check Continue shopping button'
        );

        $this->assertElementPresent(
            "//div[@id='cart']"
            . "/div[@id='shopping-cart']"
            . "/div[@class='cart-buttons']"
            . "/form[@method='post']"
            . "/div"
            . "/a[@class='clear-bag' and text()='Clear bag']",
            'check Clear bag link'
        );

        $this->assertElementPresent(
            "//div[@id='cart']"
            . "/div[@id='shopping-cart']"
            . "/ul[@class='sums']"
            . "/li[@class='subtotal']"
            . "/strong[text()='Subtotal:']",
            'check Subtotal'
        );

        $cnt = $this->getXpathCount(
            "//div[@id='cart']"
            . "/div[@id='cart-right']"
            . "/ul[@class='totals']"
            . "/li"
        );
        $this->assertEquals(5, $cnt, 'check totals rows count');

        $this->assertElementPresent(
            "//div[@id='cart']"
            . "/div[@id='cart-right']"
            . "/ul[@class='totals']"
            . "/li[@class='subtotal']"
            . "/strong[text()='Subtotal:']",
            'check Subtotal #2'
        );

        $this->assertElementPresent(
            "//div[@id='cart']"
            . "/div[@id='cart-right']"
            . "/ul[@class='totals']"
            . "/li[@class='shipping-modifier']"
            . "/strong[text()='Shipping cost:']",
            'check Shipping cost'
        );

        $this->assertElementPresent(
            "//div[@id='cart']"
            . "/div[@id='cart-right']"
            . "/ul[@class='totals']"
            . "/li[@class='tax-modifier']"
            . "/strong[text()='tax:']",
            'check Tax cost'
        );

        $this->assertElementPresent(
            "//div[@id='cart']"
            . "/div[@id='cart-right']"
            . "/ul[@class='totals']"
            . "/li[@class='total']"
            . "/strong[text()='Total:']",
            'check Total'
        );

        $this->assertElementPresent(
            "//div[@id='cart']"
            . "/div[@id='cart-right']"
            . "/ul[@class='totals']"
            . "/li[@class='button']"
            . "/button[@class='bright']"
            . "/span[text()='Go to checkout']",
            'check Checkout button'
        );

        $this->assertElementPresent(
            "//div[@id='cart']"
            . "/div[@id='cart-right']"
            . "/div[@class='box']"
            . "/div[@class='estimator']"
            . "/form[@method='get']"
            . "/div"
            . "/button",
            'check Shipping estimator button'
        );
    }

    public function testRemove()
    {
        $product = $this->addToCart();

        $this->click(
            "//td[@class='item-remove delete-from-list']"
            . "/form[@method='post']"
            . "/div"
            . "/input[@type='image']",
            'check remove item'
        );

        $this->waitForLocalCondition(
            'jQuery("h1#page-title").html() == "Your shopping bag is empty"',
            30000,
            'check remove'
        );
    }

    public function testUpdateQuantity()
    {
        $product = $this->addToCart();

        $this->typeKeys(
            "//td[@class='item-qty']"
            . "/form[@method='post']"
            . "/div"
            . "/input[@type='text']",
            '3'
        );

        $this->waitForLocalCondition(
            'jQuery("h1#page-title").html().search(/ 3 items/) != -1',
            30000,
            'check quantity update'
        );

        $this->typeKeys(
            "//td[@class='item-qty']"
            . "/form[@method='post']"
            . "/div"
            . "/input[@type='text']",
            '-3'
        );

        $this->waitForLocalCondition(
            'jQuery(".item-qty .quantity").parents().eq(0).find(".error").length == 1',
            30000,
            'check quantity update #2'
        );
    }

    public function testEstimator()
    {
        $product = $this->addToCart();

        $this->click(
            "//div[@id='cart']"
            . "/div[@id='cart-right']"
            . "/div[@class='box']"
            . "/div[@class='estimator']"
            . "/form[@method='get']"
            . "/div"
            . "/button",
            'check open estimator'
        );

        $this->waitForLocalCondition(
            'jQuery("h2.ajax-title-loadable").html() == "Estimate shipping cost"',
            60000,
            'check open estimator (popup)'
        );

        $this->assertElementNotPresent(
            "//h3[text()='Choose shipping method']"
        );

        $this->assertElementPresent(
            "//select[@id='destination_country']"
            . "/option[@selected='selected' and @value='US']",
            'check default country'
        );

        $this->select(
            "//select[@id='destination_country']",
            'value=US'
        );

        $this->type(
            "//input[@id='destination_zipcode']",
            '10001'
        );

        $this->click(
            "//form[@class='estimator']"
            . "/div"
            . "/button[@type='submit']",
            'set destination'
        );
    
        $this->waitForLocalCondition(
            'jQuery(".estimate-methods h3").html() == "Choose shipping method"',
            60000,
            'check reload estimator (popup)'
        );

        $this->assertElementPresent(
            "//h3[text()='Choose shipping method']"
        );

        $name = $this->getJSExpression('jQuery(".estimate-methods form ul li:eq(1) label ").html()');

        $this->check(
            "//div[@class='estimate-methods']"
            . "/form[@method='post']"
            . "/ul"
            . "/li[position()=2]"
            . "/input[@type='radio']",
            'check 2nd shipping method'
        );

        $this->click(
            "//div[@class='estimate-methods']"
            . "/form[@method='post']"
            . "/div"
            . "/button[@type='submit']",
            'change method'
        );

        $this->waitForLocalCondition(
            'jQuery(".box .estimator ul li").html()',
            60000,
            'check close estimator'
        );

        $this->waitForLocalCondition(
            'jQuery(".box .estimator ul li").html().search(/' . $name . '/) != -1',
            3000,
            'check close estimator and set shipping method'
        );

        $this->assertElementNotPresent(
            "//h2[text()='Estimate shipping cost']"
        );

        $this->assertElementPresent(
            "//div[@class='box']"
            . "/div[@class='estimator']"
            . "/ul"
            . "/li"
            . "/span[text()='Shipping:']",
            'check shipping box'
        );

        $this->assertElementPresent(
            "//div[@class='box']"
            . "/div[@class='estimator']"
            . "/ul"
            . "/li"
            . "/span[text()='Estimated for:']",
            'check address box'
        );

        $this->assertElementPresent(
            "//div[@class='box']"
            . "/div[@class='estimator']"
            . "/ul"
            . "/li[contains(text(),'United States, AL, 10001')]",
            'check address'
        );

        $this->assertElementPresent(
            "//div[@class='box']"
            . "/div[@class='estimator']"
            . "/div[@class='link']"
            . "/a[@class='estimate' and text()='Change method']",
            'check box link'
        );
    }

    public function testClear()
    {
        $product = $this->addToCart();

        $this->click(
            "//a[@class='clear-bag' and text()='Clear bag']",
            'check clear bag'
        );

        $this->waitForLocalCondition(
            'jQuery("h1#page-title").html() == "Your shopping bag is empty"',
            30000,
            'check remove'
        );
    }

    public function testContinueShopping()
    {
        $product = $this->addToCart();

        $this->clickAndWait(
            "//div[@id='cart']"
            . "/div[@id='shopping-cart']"
            . "/div[@class='cart-buttons']"
            . "/button[@class='action']",
            'check Continue shopping'
        );

        $pid = null;
        if (preg_match('/product_id-(\d+)/Ss', $this->getLocation(), $m)) {
            $pid = intval($m[1]);
        }

        $this->assertEquals($product->getProductId(), $pid, 'check product id');
    }
}
