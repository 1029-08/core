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
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

/**
 * Abstract Object id widget parameter
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
abstract class XLite_Model_WidgetParam_ObjectId extends XLite_Model_WidgetParam_Int
{
    /**
     * Return object class name 
     * 
     * @var    string
     * @access protected
     * @since  3.0.0 EE
     */
    abstract protected function getClassName();

    /**
     * Return object ID
     * 
     * @param int $id object ID
     *  
     * @return int
     * @access protected
     * @since  3.0.0 EE
     */
    protected function getId($id = null)
    {
        return isset($id) ? $id : $this->value;
    }

    /**
     * Return list of conditions to check
     *
     * @param mixed $value value to validate
     *
     * @return void
     * @access protected
     * @since  3.0.0 EE
     */
    protected function getValidaionSchema($value)
    {
        $schema = parent::getValidaionSchema($value);

        $schema[] = array(
            self::ATTR_CONDITION => 0 > $value,
            self::ATTR_MESSAGE   => ' is a negative number',
        );

        return $schema;
    }

    /**
     * Return object with passed/predefined ID
     *
     * @param int $id object ID
     *
     * @return XLite_Base
     * @access public
     * @since  3.0.0
     */
    public function getObject($id = null)
    {
        $id = $this->getId($id);
        return XLite_Model_CachingFactory::getObject(
            __METHOD__ . $this->getClassName() . $id,
            $this->getClassName(),
            array($id)
        );
    }
}

