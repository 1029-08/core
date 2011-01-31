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
 * @subpackage Model
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Module\CDev\PayFlowLink\Model\PaymentMethod;

/**
 * ____description____
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class Payflowlink extends \XLite\Model\PaymentMethod\CreditCard
{
    public $processorName = "PayFlowLink";
    public $configurationTemplate = "modules/CDev/PayFlowLink/config.tpl";
    public $formTemplate ="modules/CDev/PayFlowLink/checkout.tpl";

    public function __construct($id = null) 
    {
        parent::__construct($id);
        if ($id) {
            if (!$this->get('params')) {
                $this->set('params', array());
            }
            if (!$this->getComplex('params.gateway_url')) {
                $this->setComplex('params.gateway_url', "https://payflowlink.paypal.com");
            }
        }
    }

    function handleRequest(\XLite\Model\Cart $cart)
    {
        require_once LC_MODULES_DIR . 'PayFlowLink' . LC_DS . 'encoded.php';
        PaymentMethod_PayFlowLink_handleRequest($this, $cart);
    }

    function getOrderId($cart)
    {
        return $cart->get('order_id');
    }

    function getPaymentURL($cart)
    {
        return $this->xlite->ShopUrl('classes/XLite/Module/PayFlowLink/redirect.php');
    }
}
