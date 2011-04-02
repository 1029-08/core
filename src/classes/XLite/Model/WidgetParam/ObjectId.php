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

namespace XLite\Model\WidgetParam;

/**
 * Abstract Object id widget parameter
 * 
 * @see   ____class_see____
 * @since 3.0.0
 */
abstract class ObjectId extends \XLite\Model\WidgetParam\Int
{
    /**
     * Return object class name 
     * 
     * @return string
     * @see    ____func_see____
     * @since  3.0.0
     */
    abstract protected function getClassName();


    /**
     * Return object with passed/predefined ID
     *
     * @param integer $id Object ID OPTIONAL
     *
     * @return \XLite\Model\AEntity
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getObject($id = null)
    {
        return \XLite\Core\Database::getRepo($this->getClassName())->find($this->getId($id));
    }


    /**
     * getIdValidCondition 
     * 
     * @param mixed $value Value to check
     *  
     * @return array
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getIdValidCondition($value)
    {
        return array(
            self::ATTR_CONDITION => 0 >= $value,
            self::ATTR_MESSAGE   => ' is a non-positive number',
        );
    }

    /**
     * getObjectExistsCondition 
     * 
     * @param mixed $value Value to check
     *  
     * @return array
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getObjectExistsCondition($value)
    {
        return array(
            self::ATTR_CONDITION => !$this->getObject($value)->isExists(),
            self::ATTR_MESSAGE   => ' record with such ID is not found',
        );
    }

    /**
     * Return object ID
     * 
     * @param integer $id Object ID OPTIONAL
     *  
     * @return integer 
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getId($id = null)
    {
        return isset($id) ? $id : $this->value;
    }

    /**
     * Return list of conditions to check
     *
     * @param mixed $value Value to validate
     *
     * @return void
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getValidaionSchema($value)
    {
        $schema = parent::getValidaionSchema($value);
        $schema[] = $this->getIdValidCondition($value);
        $schema[] = $this->getObjectExistsCondition($value);

        return $schema;
    }
}
