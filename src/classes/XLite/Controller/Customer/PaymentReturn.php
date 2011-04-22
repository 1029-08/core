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

namespace XLite\Controller\Customer;

/**
 * Web-based payment method return
 * 
 * @see   ____class_see____
 * @since 1.0.0
 */
class PaymentReturn extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Handles the request
     * 
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function handleRequest()
    {
        \XLite\Core\Request::getInstance()->action = 'return';

        parent::handleRequest();
    }


    /**
     * This controller is always accessible
     * TODO - check if it's really needed; remove if not
     * 
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function checkStorefrontAccessability()
    {
        return true;
    }

    /**
     * Process return
     * 
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function doActionReturn()
    {
        $txn = null;
        $txnIdName = 'txnId';

        if (isset(\XLite\Core\Request::getInstance()->txn_id_name)) {
            /**
             * some of gateways can't accept return url on run-time and
             * use the one set in merchant account, so we can't pass
             * 'order_id' in run-time, instead pass the order id parameter name
             */
            $txnIdName = \XLite\Core\Request::getInstance()->txn_id_name;
        }

        if (isset(\XLite\Core\Request::getInstance()->$txnIdName)) {
            $txn = \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')
                ->find(\XLite\Core\Request::getInstance()->$txnIdName);
        }

        if (!$txn) {

            $methods = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findAllActive();

            foreach ($methods as $method) {

                if (method_exists($method->getProcessor(), 'getCallbackOwnerTransaction')) {

                    $txn = $method->getProcessor()->getReturnOwnerTransaction();
                    
                    if ($txn) {
                        break;
                    }
                }
            }

        }

        if ($txn) {
            $txn->getPaymentMethod()->getProcessor()->processReturn($txn);

            \XLite\Core\Database::getEM()->persist($txn);
            \XLite\Core\Database::getEM()->flush();

            $url = \XLite::getShopURL(
                $this->buildURL('checkout', 'return', array('order_id' => $txn->getOrder()->getOrderId())),
                \XLite\Core\Config::getInstance()->Security->customer_security
            );

            switch ($txn->getPaymentMethod()->getProcessor()->getReturnType()) {
                case \XLite\Model\Payment\Base\WebBased::RETURN_TYPE_HTML_REDIRECT:
                    $this->doHTMLRedirect($url);
                    break;

                case \XLite\Model\Payment\Base\WebBased::RETURN_TYPE_CUSTOM:
                    $txn->getPaymentMethod()->getProcessor()->doCustomReturnRedirect();
                    break;

                default:
                    $this->setReturnURL($url);
            }

        } else {
            // TODO - add error logging

            $this->setReturnURL(
                $this->buildURL('checkout')
            );
        }

    }

    /**
     * Do HTML-based redirect 
     * 
     * @param string  $url  URL
     * @param integer $time Redirect delay OPTIONAL
     *  
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function doHTMLRedirect($url, $time = 1)
    {
        $html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Refresh" content="$time;URL=$url" />
</head>
<body>
If the page is not updated in $time; seconds, please follow this link: <a href="$url">continue &gt;&gt;</a>
</body>
</html>
HTML;

        print ($html);
        exit (0);
    }
}
