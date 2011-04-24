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
 * @since     1.0.0
 */

namespace XLite\Model\Payment\Base;

/**
 * Processor 
 * 
 * @see   ____class_see____
 * @since 1.0.0
 */
abstract class Processor extends \XLite\Base
{
    /**
     * Payment procedure result codes
     */
    const PROLONGATION = 'R';
    const COMPLETED    = 'S';
    const PENDING      = 'P';
    const FAILED       = 'F';


    /**
     * Transaction (cache)
     * 
     * @var   \XLite\Model\Payment\Transaction
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $transaction;

    /**
     * Request cell with transaction input data
     * 
     * @var   array
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $request;


    /**
     * Do initial payment 
     * 
     * @return string Status code
     * @see    ____func_see____
     * @since  1.0.0
     */
    abstract protected function doInitialPayment();


    /**
     * Pay 
     * 
     * @param \XLite\Model\Payment\Transaction $transaction Transaction
     * @param array                            $request     Input data request OPTIONAL
     *  
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function pay(\XLite\Model\Payment\Transaction $transaction, array $request = array())
    {
        $this->transaction = $transaction;
        $this->request = $request;

        $this->saveInputData();

        return $this->doInitialPayment();
    }

    /**
     * Get input template 
     * 
     * @return string|void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getInputTemplate()
    {
        return null;
    }

    /**
     * Get input errors 
     * 
     * @param array $data Input data
     *  
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getInputErrors(array $data)
    {
        return array();
    }

    /**
     * Get settings widget or template 
     * 
     * @return string Widget class name or template path
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getSettingsWidget()
    {
        return null;
    }

    /**
     * Check - payment method is configured or not
     * 
     * @param \XLite\Model\Payment\Method $method Payment method
     *  
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        return true;
    }


    /**
     * Get current transaction order 
     * 
     * @return \XLite\Model\Order
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getOrder()
    {
        return $this->transaction->getOrder();
    }

    /**
     * Get current transaction order profile
     * 
     * @return \XLite\Model\Profile
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getProfile()
    {
        return $this->transaction->getOrder()->getProfile();
    }

    /**
     * Get setting value by name
     * 
     * @param string $name Name
     *  
     * @return mixed
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getSetting($name)
    {
        return $this->transaction->getPaymentMethod()->getSetting($name);
    }

    /**
     * Save input data 
     * 
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function saveInputData()
    {
        $labels = $this->getInputDataLabels();
        $accessLevels = $this->getInputDataAccessLevels();

        foreach ($this->request as $name => $value) {
            if (isset($accessLevels[$name])) {
                $this->setDetail(
                    $name,
                    $value,
                    isset($labels[$name]) ? $labels[$name] : null
                );
            }
        }
    }

    /**
     * Set transaction detail record
     * 
     * @param string $name  Code
     * @param string $value Value
     * @param string $label Label OPTIONAL
     *  
     * @return \XLite\Model\Payment\TransactionData
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function setDetail($name, $value, $label = null)
    {
        $record = new \XLite\Model\Payment\TransactionData;

        $record->setName($name);
        $record->setValue($value);
        if (isset($label)) {
            $record->setLabel($label);
        }

        $this->transaction->getData()->add($record);
        $record->setTransaction($this->transaction);

        \XLite\Core\Database::getEM()->persist($record);
    
        return $record;
    }

    /**
     * Get input data labels list
     * 
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getInputDataLabels()
    {
        return array();
    }

    /**
     * Get input data access levels list
     * 
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getInputDataAccessLevels()
    {
        return array();
    }
}
