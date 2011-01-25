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
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Module\CDev\WholesaleTrading\Controller\Admin;

/**
 * ____description____
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class Order extends \XLite\Controller\Admin\Order implements \XLite\Base\IDecorator
{
    function isShowWholesalerFields()
    {
        if (
            $this->xlite->config->CDev->WholesaleTrading->WholesalerFieldsTaxId   == "Y" ||
            $this->xlite->config->CDev->WholesaleTrading->WholesalerFieldsVat     == "Y" ||
            $this->xlite->config->CDev->WholesaleTrading->WholesalerFieldsGst     == "Y" ||
            $this->xlite->config->CDev->WholesaleTrading->WholesalerFieldsPst     == "Y"
            ) {
                return true;
            }
            return false;
    }

    function getOrder()
    {
        // AOM
        if ($this->page == "order_edit" || $this->page == "order_preview") {
            if ($this->action != "save_changes") {
                $this->xlite->set('dontStoreWholesale', true);
            }
        } else {
            $this->xlite->set('useStoredWholesale', true);
        }
        return parent::getOrder();
    }
}
