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
 * @since     3.0.0
 */

namespace XLite\View;

/**
 * Orders search widget
 * 
 * @see   ____class_see____
 * @since 3.0.0
 *
 * @ListChild (list="center")
 */
class OrderSearch extends \XLite\View\Dialog
{
    /**
     * Orders list (cache)
     * 
     * @var   array
     * @see   ____var_see____
     * @since 3.0.0
     */
    protected $orders = null;
    /**
     * Orders total count 
     * 
     * @var   integer
     * @see   ____var_see____
     * @since 3.0.0
     */
    protected $totalCount = null;

    /**
     * Conditions (cache)
     * 
     * @var   array
     * @see   ____var_see____
     * @since 3.0.0
     */
    protected $conditions = null;


    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'order_list';
    
        return $result;
    }


    /**
     * Get condition 
     * 
     * @param string $name Condition name
     *  
     * @return mixed
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getCondition($name)
    {
        return $this->getConditions()->$name;
    }

    /**
     * Check - used conditions is default or not
     * 
     * @return boolean
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function isDefaultConditions()
    {
        return false;
    }

    /**
     * Get orders 
     * 
     * @return array(\XLite\Model\Order)
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getOrders()
    {
        if (!isset($this->orders)) {
            $this->orders = \XLite\Core\Database::getRepo('\XLite\Model\Order')->search(
                $this->getConditions()
            );
        }

        return $this->orders;
    }

    /**
     * Get orders list count 
     * 
     * @return integer
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getCount()
    {
        return count($this->getOrders());
    }

    /**
     * Get total count 
     * 
     * @return integer
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getTotalCount()
    {
        if (is_null($this->totalCount)) {
            $this->totalCount = count($this->getOrders());
        }

        return $this->totalCount;
    }

    /**
     * Register JS files
     *
     * @return array
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        // TODO JS search 
        // $list[] = 'order/search/search.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'order/search/search.css';

        return $list;
    }


    /**
     * Return title
     *
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getHead()
    {
        return $this->getTotalCount() . ' orders';
    }

    /**
     * Return templates directory name
     *
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getDir()
    {
        return 'order/search';
    }

    /**
     * Get conditions 
     * 
     * @return array
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getConditions()
    {
        if (!isset($this->conditions)) {

            $this->conditions = $this->session->get('orders_search');

            if (!is_array($this->conditions)) {
                $this->conditions = array();
                $this->session->set('orders_search', $this->conditions);
            }

        }

        $cnd = new \XLite\Core\CommonCell();

        if ($this->getProfile()->isAdmin()) {
            if (!empty(\XLite\Core\Request::getInstance()->profile_id)) {
                $cnd->profileId = \XLite\Core\Request::getInstance()->profile_id;
            }
        } else {
            $cnd->profileId = $this->getProfile()->getProfileId();
        }

        if (!isset($this->conditions['sortCriterion']) || !$this->conditions['sortCriterion']) {
            $this->conditions['sortCriterion'] = 'order_id';
        }

        if (!isset($this->conditions['sortOrder']) || !$this->conditions['sortOrder']) {
            $this->conditions['sortOrder'] = 'ASC';
        }

        $cnd->orderBy = array('o.' . $this->conditions['sortCriterion'], $this->conditions['sortOrder']);

        if (isset($this->conditions['order_id'])) {
            $this->cnd->orderId = $this->conditions['order_id'];
        }   

        if (isset($this->conditions['status'])) {
            $this->cnd->status = $this->conditions['status'];
        }   

        $start = isset($this->conditions['startDate']) ? $this->conditions['startDate'] : 0;
        $end   = isset($this->conditions['endDate']) ? $this->conditions['endDate'] : 0;

        if ($start < $end) {
            $cnd->date = array($start, $end);
        }

        return $cnd;
    }

    /**
     * Get profile
     *
     * @return \XLite\Model\Profile
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getProfile()
    {
        $result = null;

        if (\XLite::isAdminZone()) {

            if (\XLite\Core\Request::getInstance()->profile_id) {

                $result = \XLite\Core\Database::getRepo('XLite\Model\Profile')
                    ->find(\XLite\Core\Request::getInstance()->profile_id);

                if (!$result->isExists()) {
                    $result = null;
                }
            }

        } else {

            $result = \XLite\Core\Auth::getInstance()->getProfile(\XLite\Core\Request::getInstance()->profile_id);

            if (!$result) {
                $result = \XLite\Core\Auth::getInstance()->getProfile();
            }
        }

        return $result;
    }
}
