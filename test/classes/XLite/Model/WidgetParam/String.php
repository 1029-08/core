<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * ____file_title____
 *  
 * @category   Lite Commerce
 * @package    Lite Commerce
 * @subpackage ____sub_package____
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2009 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @version    SVN: $Id$
 * @link       http://www.qtmsoft.com/
 * @since      3.0.0 EE
 */

/**
 * Some string
 *
 * @package    Lite Commerce
 * @subpackage Model
 * @since      3.0
 */
class XLite_Model_WidgetParam_String extends XLite_Model_WidgetParam_Abstract
{
	/**
     * Param type
     *
     * @var    string
     * @access protected
     * @since  3.0
     */
    protected $type = 'string';

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
        return array(
            array(
                self::ATTR_CONDITION => !is_string($value),
                self::ATTR_MESSAGE   => ' is not a string',
            ),
        );
    }
}

