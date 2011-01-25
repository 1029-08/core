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

namespace XLite\Controller\Admin;

/**
 * Shipping settings management page controller
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class ShippingSettings extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Common method to determine current location
     *
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getLocation()
    {
        return 'Shipping settings';
    }

    /**
     * getOptionsCategory 
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getOptionsCategory()
    {
        return 'Shipping';
    }

    /**
     * Returns shipping options 
     * 
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getOptions()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Config')
            ->findByCategoryAndVisible($this->getOptionsCategory());
    }

    /**
     * Do action 'Update'
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function doActionUpdate()
    {
        $postedData = \XLite\Core\Request::getInstance()->getData();

        $options = \XLite\Core\Database::getRepo('\XLite\Model\Config')
            ->findBy(array('category' => $this->getOptionsCategory()));

        $isUpdated = false;

        foreach ($options as $key => $option) {

            $name = $option->getName();
            $type = $option->getType();

            if (isset($postedData[$name]) || 'checkbox' == $type) {

                if ('checkbox' == $type) {
                    $option->setValue(isset($postedData[$name]) ? 'Y' : 'N');

                } else {
                    $option->setValue($postedData[$name]);
                }

                $isUpdated = true;
                \XLite\Core\Database::getEM()->persist($option);
            }
        }

        if ($isUpdated) {
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * getStateById
     *
     * @param mixed $stateId ____param_comment____
     *
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getStateById($stateId)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\State')->find($stateId);
    }
}
