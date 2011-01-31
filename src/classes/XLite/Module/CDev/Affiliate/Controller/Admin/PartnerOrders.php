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
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Module\CDev\Affiliate\Controller\Admin;

/**
 * ____description____
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class PartnerOrders extends \XLite\Controller\Admin\AAdmin
{
    public $params = array('target', 'mode', 'order_id1', 'order_id2', 'partner_id', 'status', 'payment_status');
    public $crlf = "\r\n";

    function action_export() 
    {
        $w = new \XLite\View\AView();
        $w->component = $this;
        $w->set('template', "modules/CDev/Affiliate/orders.tpl");
        $this->startDownload('orders.csv');
        $w->init();
        $w->display();

        // do not output anything
        $this->set('silent', true);
    }
    
    function getDelimiter() 
    {
        global $DATA_DELIMITERS;
        return $DATA_DELIMITERS[$this->delimiter];
    }

    function getSales() 
    {
        if (is_null($this->sales)) {
            $pp = new \XLite\Module\CDev\Affiliate\Model\PartnerPayment();
            $this->sales = $pp->searchSales(
                    $this->get('startDate'),
                    $this->get('endDate') + 24 * 3600,
                    null,
                    $this->get('partner_id'),
                    $this->get('payment_status'),
                    $this->get('status'),
                    $this->get('order_id1'),
                    $this->get('order_id2'),
                    true // show affiliate sales
                    );
            $this->salesCount = count($this->sales);
        }
        return $this->sales;
    }

    function getSalesCount() 
    {
        return count($this->get('sales'));
    }
    
    function getOrder() 
    {
        return $this->getComplex('sale.order');
    }
    
    function fillForm() 
    {
        if (!isset($this->startDate)) {
            $date = getdate(time());
            $this->set('startDate', mktime(0,0,0,$date['mon'],1,$date['year']));
        }
        if (!isset($this->partner_id)) {
            $this->set('partner_id', "");
        }
        parent::fillForm();
    }
}
