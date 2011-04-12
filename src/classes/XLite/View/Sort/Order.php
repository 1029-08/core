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

namespace XLite\View\Sort;

/**
 * Order sort widget 
 * 
 * @see   ____class_see____
 * @since 1.0.0
 *
 * @ListChild (list="orders.panel", weight="20")
 */
class Order extends \XLite\View\Sort\ASort
{
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

        $this->widgetParams[self::PARAM_PARAMS]->setValue(array('target' => 'order_list', 'mode' => 'search'));
        $this->widgetParams[self::PARAM_SORT_CRITERIONS]->setValue(
            array(
                'order_id' => 'Order id',
                'date'     => 'Date',
                'status'   => 'Status',
                'total'    => 'Total',
            )
        );
        $this->widgetParams[self::PARAM_CELL]->setValue($this->session->get('orders_search'));
    }
}
