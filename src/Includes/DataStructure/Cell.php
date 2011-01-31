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
 * @subpackage Includes
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace Includes\DataStructure;

/**
 * Common cell
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class Cell
{
    /**
     * Array of properties 
     * 
     * @var    array
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $properties = array();


    /**
     * Constructor 
     * 
     * @param array $data data to set
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function __construct(array $data = null)
    {
        !isset($data) ?: $this->setData($data);
    }

    /**
     * Get property by name
     * 
     * @param string $name property name
     *  
     * @return mixed
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function __get($name)
    {
        return isset($this->properties[$name]) ? $this->properties[$name] : null;
    }

    /**
     * Set property value
     * 
     * @param string $name  property name
     * @param mixed  $value property value
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function __set($name, $value)
    {
        $this->properties[$name] = $value;
    }

    /**
     * Check if property exists
     * 
     * @param string $name property name
     *  
     * @return bool
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function __isset($name)
    {
        return isset($this->properties[$name]);
    }

    /**
     * Unset property
     * 
     * @param string $name property name
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function __unset($name)
    {
        unset($this->properties[$name]);
    }


    /**
     * Return all properties
     * 
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getData()
    {
        return $this->properties;
    }

    /**
     * Append data 
     * 
     * @param array $data data to set
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function setData(array $data)
    {
        $this->properties = array_replace_recursive($this->properties, $data);
    }
}
