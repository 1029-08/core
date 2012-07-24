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
 * @copyright Copyright (c) 2011-2012 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 */

namespace XLite\Module\CDev\ProductOptions\View;

/**
 * Modify option groups exceptions
 *
 * @see   ____class_see____
 * @since 1.0.0
 */
class ModifyExceptions extends \XLite\View\AView
{
    /**
     * Exceptions (cache)
     *
     * @var   array
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $exceptions;


    /**
     * Get product id
     *
     * @return integer
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getProductId()
    {
        return $this->getProduct()->getProductId();
    }

    /**
     * Get option groups
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getGroups()
    {
        $list = array();

        foreach ($this->getProduct()->getOptionGroups() as $group) {
            if ($group::TEXT_TYPE != $group->getType()) {
                $list[] = $group;
            }
        }

        return $list;
    }

    /**
     * Get exceptions list
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getExceptions()
    {
        if (!isset($this->exceptions)) {
            $this->exceptions = array();

            foreach ($this->getGroups() as $group) {
                foreach ($group->getOptions() as $option) {
                    foreach ($option->getExceptions() as $e) {
                        $eid = $e->getExceptionId();
                        if (!isset($this->exceptions[$eid])) {
                            $this->exceptions[$eid] = array();
                        }

                        $this->exceptions[$eid][$group->getGroupId()] = $option->getOptionId();
                    }
                }
            }

            ksort($this->exceptions);
        }

        return $this->exceptions;
    }

    /**
     * Check - is not option group part of specified exception or not
     *
     * @param array                                               $exception Exception cell
     * @param \XLite\Module\CDev\ProductOptions\Model\OptionGroup $group     Option group
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function isNotPartException(array $exception, \XLite\Module\CDev\ProductOptions\Model\OptionGroup $group)
    {
        return !isset($exception[$group->getGroupId()]);
    }

    /**
     * Check - is option selected in specified exception or not
     *
     * @param array                                          $exception Exception cell
     * @param \XLite\Module\CDev\ProductOptions\Model\Option $option    Option
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function isOptionSelected(array $exception, \XLite\Module\CDev\ProductOptions\Model\Option $option)
    {
        return isset($exception[$option->getGroup()->getGroupId()])
            && $exception[$option->getGroup()->getGroupId()] == $option->getOptionId();
    }

    /**
     * Get empty exception cell
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getEmptyException()
    {
        return array();
    }


    /**
     * Return widget default template
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/ProductOptions/exceptions.tpl';
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
        return parent::isVisible()
            && $this->getProduct()->getOptionGroups()->count();
    }
}
