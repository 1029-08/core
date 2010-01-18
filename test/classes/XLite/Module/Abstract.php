<?php

abstract class XLite_Module_Abstract extends XLite_Model_Module
{
	/**
	 * Return module name by class name
	 * 
	 * @return string
	 * @access protected
	 * @since  3.0
	 */
	protected function getModuleName()
	{
		return preg_match('/XLite_Module_(\w+)_Main/', get_class($this), $matches) ?
			$matches[1] : $this->_die('Module class name is invalid - "' . get_class($this) . '"');
	}

	/**
	 * Easy way to register payment method
	 * 
	 * @param string $name payment method name
	 *  
	 * @return void
	 * @access protected
	 * @since  3.0
	 */
	protected function registerPaymentMethod($name)
	{
		$method = new XLite_Model_PaymentMethod();
		$class  = 'Module_' . $this->getModuleName() . '_Model_PaymentMethod_' . XLite_Core_Converter::convertToCamelCase($name);

		return $method->registerMethod($name, $class);
	}

	/**
	 * Easy way to register shipping module 
	 * 
	 * @param string $name shipping module name
	 *  
	 * @return void
	 * @access protected
	 * @since  3.0
	 */
	protected function registerShippingModule($name)
	{
		$shipping = new XLite_Model_Shipping();
		$class  = 'Module_' . $this->getModuleName() . '_Model_Shipping_' . XLite_Core_Converter::convertToCamelCase($name);

		return $shipping->registerShippingModule($name, $class);
	}


	/**
     * Method to initialize concrete module instance
     *
     * @return void
     * @access public
     * @since  3.0
     */
    public function init()
    {
    }

	/**
	 * Return module type 
	 * 
	 * @return int
	 * @access public
	 * @since  3.0
	 */
	public static function getType()
	{
		return self::MODULE_3RD_PARTY;
	}

	/**
	 * Return module version
	 * 
	 * @return string
	 * @access public
	 * @since  3.0
	 */
	public static function getVersion()
	{
		return '1.0';
	}

	/**
	 * Return module description 
	 * 
	 * @return string
	 * @access public
	 * @since  3.0
	 */
	public static function getDescription()
	{
		return '';
	}

	/**
	 * Determines if we need to show settings form link
	 * 
	 * @return bool
	 * @access public
	 * @since  3.0
	 */
	public static function showSettingsForm()
    {
		return false;
    }

	/**
	 * Return link to settings form.
	 * See Model/Module.php
	 * 
	 * @return mixed
	 * @access public
	 * @since  3.0
	 */
	public function getSettingsForm()
    {
		return null;
    }

	/**
     * Return module dependencies
     *
     * @return array
     * @access public
     * @since  3.0
     */
    public static function getDependencies()
    {
        return array();
    }

	/**
     * Check if current module depends on a passed one
     *
     * @param string $moduleName module to check
     *
     * @return bool
     * @access public
     * @since  1.0
     */
    public static function isDependsOn($moduleName)
    {
        return in_array($moduleName, self::getDependencies());
    }
}

