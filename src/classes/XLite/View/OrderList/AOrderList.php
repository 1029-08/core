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
 * @link      http://www.litecommerce.com/
 * @see       ____file_see____
 * @since     1.0.0
 */

namespace XLite\View\OrderList;

/**
 * Abstract order list
 *
 * @see   ____class_see____
 * @since 1.0.0
 */
abstract class AOrderList extends \XLite\View\Dialog
{
    /**
     * Orders list (cache)
     *
     * @var   array(\XLite\Model\Order)
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $orders = null;

    /**
     * Widget class name
     *
     * @var   string
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $widgetClass = '';

    /**
     * Get orders
     *
     * @return array(\XLite\Model\Order)
     * @see    ____func_see____
     * @since  1.0.0
     */
    abstract public function getOrders();

    /**
     * Get widget keys
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    abstract protected function getWidgetKeys();

    /**
     * Register CSS files
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'order/list/list.css';

        return $list;
    }

    /**
     * Get class identifier as CSS class name
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getClassIdentifier()
    {
        return strtolower(str_replace('_', '-', $this->widgetClass));
    }

    /**
     * Get AJAX request parameters as javascript object definition
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getAJAXRequestParamsAsJSObject()
    {
        $params = $this->getAJAXRequestParams();

        $result = array();
        $forbidden = array('widgetTarget', 'widgetAction', 'widgetClass');

        foreach ($params as $key => $value) {
            if (!in_array($key, $forbidden)) {
                $result[] = $key . ': \'' . $value . '\'';
            }
        }

        return '{ '
            . 'widgetTarget: \'' . $params['widgetTarget'] . '\', '
            . 'widgetAction: \'' . $params['widgetAction'] . '\', '
            . 'widgetClass: \'' . $params['widgetClass'] . '\', '
            . 'widgetParams: { ' . implode(', ', $result) . ' }'
            . ' }';
    }

    /**
     * Check if the product of the order item is deleted one in the store
     *
     * @param \XLite\Model\OrderItem $item
     * @param boolean                $data
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function checkIsAvailableToOrder(\XLite\Model\OrderItem $item, $data)
    {
        return $data !== $item->isValid();
    }

    /**
     * Return title
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getHead()
    {
        return null;
    }

    /**
     * Return templates directory name
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getDir()
    {
        return 'order/list';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getOrders();
    }

    /**
     * Get AJAX request parameters
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getAJAXRequestParams()
    {
        $params = array(
            'widgetTarget' => \XLite\Core\Request::getInstance()->target,
            'widgetAction' => \XLite\Core\Request::getInstance()->action,
            'widgetClass'  => $this->widgetClass,
        );

        return $params + $this->getWidgetKeys();
    }

    /**
     * Check if the re-order button is shown
     *
     * @param \XLite\Model\Order $order
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function showReorder(\XLite\Model\Order $order)
    {
        return (bool)\Includes\Utils\ArrayManager::findValue($order->getItems(), array($this, 'checkIsAvailableToOrder'), false);
    }
}
