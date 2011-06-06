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

namespace XLite\Model\WidgetParam;

/**
 * Set
 *
 * @see   ____class_see____
 * @since 1.0.0
 */
class Set extends \XLite\Model\WidgetParam\String
{
    /**
     * Param type
     *
     * @var   string
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $type = 'list';

    /**
     * Options
     *
     * @var   array
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $options = null;


    /**
     * Constructor
     *
     * @param mixed $label     Param label (text)
     * @param mixed $value     Default value OPTIONAL
     * @param mixed $isSetting Display this setting in CMS or not OPTIONAL
     * @param array $options   Options list OPTIONAL
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function __construct($label, $value = null, $isSetting = false, array $options = array())
    {
        parent::__construct($label, $value, $isSetting);

        // TODO - check if there are more convinient ways to extend this class
        if (!isset($this->options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Set options 
     * 
     * @param array $options Options
     *  
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Return list of conditions to check
     *
     * @param mixed $value Value to validate
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getValidaionSchema($value)
    {
        return parent::getValidaionSchema($value) + array(
            array(
                self::ATTR_CONDITION => isset($this->options[$value]),
                self::ATTR_MESSAGE   => ' unallowed param value - "' . $value . '"',
            ),
        );
    }
}
