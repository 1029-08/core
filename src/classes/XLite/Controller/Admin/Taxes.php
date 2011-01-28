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
 * Taxes
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class Taxes extends \XLite\Controller\Admin\AAdmin
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
        return 'Taxes';
    }

    public $params = array('target', 'page', 'mode', 'ind');

    public $page = 'options';

    public $pages = array(
        'options' => 'Tax Options',
        'rates'   => 'Rates/Conditions',
        'schemes' => 'Tax Scheme Definitions',
    );
                       
    public $pageTemplates = array(
        'options'  => 'tax/options.tpl', 
        'rates'    => 'tax/rates.tpl',    
        'schemes'  => 'tax/schemas.tpl',
        'add_rate' => 'tax/add.tpl',
    );

    public $taxes;

    public $_rates;

    public $_levels;
    
    protected $invalidExpressions = array();

    protected $invalidFormula = array();
 
    function init()
    {
        parent::init();

        if ($this->get('page') == 'add_rate') {
            $this->pages = array(
                'add_rate' => ($this->get('mode') == 'edit' ? 'Edit' : 'Add') . ' rate/condition'
            );
            $this->pageTemplates = array("add_rate" => "tax/add.tpl");
        }

        $this->taxes = new \XLite\Model\TaxRates();
        $this->getRates();

        if ($this->get('mode') == 'add') {
            $this->initRuleParams();

        } elseif ($this->get('mode') == 'edit') {
            $this->doActionEdit();
        }

        if ($this->page == 'rates' && !isset($this->action)) {
            foreach ($this->_rates as $ind_rate => $rate) {
                $value = $this->getCurrentVarValue($ind_rate, $rate);
                if (!isset($value)) {
                    $value = $this->getCurrentCondVarValue($ind_rate, $rate);
                }

                if (!isset($value)) {
                    continue;
                }
                $value = trim($value);
                $this->_validateValue($value, $ind_rate);
            }

            if (count($this->invalidExpressions) > 0) {
                $this->set('inv_exp_error', 1);
                $this->valid = false;
            }
        }
    }
       
    /**
     * Add tax 
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
       protected function doActionAddTax()    
    {
        $poses = array();
        $taxes = $this->config->Taxes->taxes;
        if (!is_array($taxes)) {
            $taxes = unserialize($taxes);
        }
        if (!is_array($taxes)) {
            $taxes = array();
        }

        $postData = \XLite\Core\Request::getInstance()->new;

        if ($postData['name']) {
            $postData['pos'] = max(0, intval($postData['pos']));
                                                                    
            $taxes[] = $postData;

            foreach ($taxes as $i => $data) {
                $poses[$i] = $data['pos'];
            }

        } else {

            // TODO - add top message

        }

        if ($poses) {
            array_multisort($poses, $taxes);

            \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption(
                array(
                    'category' => 'Taxes',
                    'name'     => 'taxes',
                    'value'    => serialize($taxes),
                    'type'     => 'serialized'
                )
            );
        }
    }
    
    /**
     * Update tax options and exists taxes
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function doActionUpdateOptions()
    {
        $taxes = array();
        $postData = \XLite\Core\Request::getInstance()->data;

        if (is_array($postData)) {
            $poses = array();
            foreach ($postData as $idx => $data) {
                $data['pos'] = max(0, intval($data['pos']));
                $poses[$idx] = $data['pos'];
                $taxes[] = $data;
            }
            array_multisort($poses, $taxes);
        }

        $schema = array(
            'taxes'               => $taxes,
            'use_billing_info'    => \XLite\Core\Request::getInstance()->use_billing_info,
            'prices_include_tax'  => \XLite\Core\Request::getInstance()->prices_include_tax,
            'include_tax_message' => \XLite\Core\Request::getInstance()->include_tax_message
        );

        $this->taxes->setSchema($schema);
    }

    /**
     * Delete tax(es)
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function doActionDeleteTax()
    {
        $deleted = \XLite\Core\Request::getInstance()->deleted;
        if (!$deleted || !is_array($deleted)) {

            // TODO - add top message

        } else {
            $taxes = array_diff_key(
                $this->config->Taxes->taxes,
                array_flip($deleted)
            );

            \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption(
                array(
                    'category' => 'Taxes',
                    'name'     => 'taxes',
                    'value'    => serialize($taxes),
                    'type'     => 'serialized'
                )
            );

            // TODO - add top message
        }
    }

    function action_reset()
    {
        // reset to a pre-defined schema
        $schemaName = \XLite\Core\Request::getInstance()->schema;
        $this->taxes->setPredefinedSchema($schemaName);
        $this->page = "options";
    }

    function action_delete_schema()
    {
        $name = \XLite\Core\Request::getInstance()->schema;
        $tax = new \XLite\Model\TaxRates();
        $tax->saveSchema($name, null);
    }
    
    function _validateValue($value, $ind_rate)
    {
        static $tax;

        if (!isset($tax)) {
            $tax = new \XLite\Model\TaxRates();
        }

        // find the corresponding cell in the rates tree
        $ptr =& $this->locateNode($this->taxes->_rates, $this->_levels[$ind_rate]);
        $tax_name = $this->getNoteTaxName($ptr);

        // check expression {{{
        if (($value{0} != '=') && !preg_match("/^\d+(\.\d+)?$/", $value)) {
            $this->error = "Tax value must be a number or contain '=' at its start: '$value'";
            unset($this->invalidExpressions[$ind_rate]);
            $this->invalidFormula[$ind_rate] = $value;
            $this->set('valid', false);
            return;

        } else {
            unset($this->invalidFormula[$ind_rate]);
        }

        if (!preg_match("/^\d+(\.\d+)?$/", $value) && !$tax->checkExpressionSyntax($value, $this->invalidExpressions[$ind_rate], $tax_name)) {
            $this->invalidExpressions[$ind_rate] = '"<b>'.join('</b>", "<b>', $this->invalidExpressions[$ind_rate]).'</b>"';
            return;

        } else {
            unset($this->invalidExpressions[$ind_rate]);
        }
        

        if (is_array($ptr)) {
            // conditional
            $ptr['action'] = $this->_insertValue($ptr['action'], $value);

        } else {
            $ptr = $this->_insertValue($ptr, $value);
        }
    }

    function action_update_rates()
    {
        // update rates
        if (isset(\XLite\Core\Request::getInstance()->varvalue)) {
            foreach (\XLite\Core\Request::getInstance()->varvalue as $ind => $value) {
                $value = trim($value);
                $this->_validateValue($value, $ind);
            }
            if (count($this->invalidExpressions) > 0) {
                $this->set('inv_exp_error', 1);
                $this->valid = false;
            }
        }

        // sort rates
        if (isset(\XLite\Core\Request::getInstance()->pos)) {

            // build a pos tree
            $posTree = array();
            foreach (\XLite\Core\Request::getInstance()->pos as $ind => $pos) {
                $levels = $this->_levels[$ind];
                array_pop($levels);

                // locate the corresponding pos array in the pos tree
                $ptr =& $this->locateNode($posTree, $levels);
                if (!isset($ptr['orderbys'])) {
                    $ptr['orderbys'] = array();
                }
                $ptr['orderbys'][$ind] = $pos;
            }

            // sort all lists recursively
            $this->_sortRates($this->taxes->_rates, $posTree);
        }

        // store
        $this->taxes->setSchema(array("tax_rates" => $this->taxes->_rates));
    }

    function action_open()
    {
        $ind = \XLite\Core\Request::getInstance()->ind;
        $node =& $this->locateNode($this->taxes->_rates, $this->_levels[$ind]);
        $node['open'] = true;

        // store
        $this->taxes->setSchema(array("tax_rates" => $this->taxes->_rates));
        $this->getRates(); // re-build the tree
    }

    function action_all()
    {
        $this->changeAll($this->taxes->_rates, \XLite\Core\Request::getInstance()->open);

        // store
        $this->taxes->setSchema(array("tax_rates" => $this->taxes->_rates));
        $this->getRates(); // re-build the tree
    }
    
    function changeAll(&$tree, $open)
    {
        for ($i = 0; $i < count($tree); $i++) {
            if ($this->isCondition($tree[$i])) {
                if ($open) {

                    $tree[$i]['open'] = true;

                } elseif (isset($tree[$i]['open'])) {
                    unset($tree[$i]['open']);
                }

                $this->changeAll($tree[$i]['action'], $open);
            }
        }
    }
   
    function action_close()
    {
        $ind = \XLite\Core\Request::getInstance()->ind;
        $node =& $this->locateNode($this->taxes->_rates, $this->_levels[$ind]);
        if (isset($node['open'])) {
            unset($node['open']);
        }
        // store
        $this->taxes->setSchema(array("tax_rates" => $this->taxes->_rates));
        $this->getRates(); // re-build the tree
    }

    /**
     * Edit tax rate
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function doActionEdit()
    {
        $this->taxes = new \XLite\Model\TaxRates();
        if (
            isset(\XLite\Core\Request::getInstance()->ind)
            && \XLite\Core\Request::getInstance()->ind !== ''
        ) {
            $ind = \XLite\Core\Request::getInstance()->ind;
            $this->ind = $ind;
            $this->tax = $this->locateNode($this->taxes->_rates, explode(',', $ind));

        } else {
            $this->tax = '';
        }

        $this->initRuleParams();
        $this->edit = 1;
    }
   
    /**
     * Add tax rate
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function doActionAdd()
    {
        $this->doActionEdit();
        $this->edit = 0;
        $this->tax = '';
    }
    
    protected function readTaxForm()
    {
        if (isset(\XLite\Core\Request::getInstance()->ind)) {
            $ind = \XLite\Core\Request::getInstance()->ind;
            $this->ind = $ind;
            $this->taxes = new \XLite\Model\TaxRates();
            if ($ind === '') {
                $ind = array();

            } else {
                $ind = explode(',', $ind);
            }
            $this->indexes = $ind;
        }

        $this->initRuleParams();
        $conjuncts = array();
        $postData = \XLite\Core\Request::getInstance()->getData();

        foreach ($this->taxParams as $param) {
            if (!isset($postData[$param['var']])) {
                continue;
            }

            if (trim($postData[$param['var']]) !== '') {
                $conjuncts[] = $param['cond'] . '=' . $postData[$param['var']];
            }
        }

        $condition = implode(' AND ', $conjuncts);
        $action = '';
        $taxValue = trim($postData['taxValue']);
        $taxName = trim($postData['taxName']);
        if ($taxName !== '' && $taxValue !== '') {
            if (is_numeric($taxValue)) {
                $action = $taxName . ':=' . $taxValue;

                if (is_array($this->tax)) {

                    $currentName = substr($this->tax['action'], 0, strpos($this->tax['action'], ':='));

                } else {
                    $currentName = substr($this->tax, 0, strpos($this->tax, ":="));
                }
                
                $tax = new \XLite\Model\TaxRates();

                if ($currentName != '' && $currentName != $taxName && $tax->isUsedInExpressions($currentName, $taxName)){
                    $this->set('error', 'Tax name "' . $currentName . '" is used in another formula.');
                    return null;
                }

            } elseif (substr($taxValue, 0, 1) == '=') {

                // check expression {{{
                $invalids = array();
                $tax = new \XLite\Model\TaxRates();
                if ($tax->checkExpressionSyntax($taxValue, $invalids, $taxName)) {
                    $action = $taxName . ':=' . $taxValue;

                } else {
                    $this->set('inv_exp_error', 1);
                    $this->set('exp_error', '"<b>'.join('</b>", "<b>', $invalids).'</b>"');
                    return null;
                }

                $currentName = '';

                if (is_array($this->tax)) {
                    $currentName = substr($this->tax['action'], 0, strpos($this->tax['action'], ':='));

                } else {
                    $currentName = substr($this->tax, 0, strpos($this->tax, ':='));
                }

                if ($currentName != '' && $currentName != $taxName && $tax->isUsedInExpressions($currentName, $taxName)){
                    $this->set('error', 'Tax name "' . $currentName . '" is used in another formula.');
                    return null;
                }
                
            } else {

                $this->error = 'Tax value must be a number or contain \'=\' at its start: \'' . $taxValue . '\'';
                return null;
            }
        }

        if ($action !== '' && $condition === '') {
            return $action;
        }

        if ($action !== '' && $condition !== '') {
            return array(
                'condition' => $condition,
                'action'    => $action,
            );
        }

        if ($action === '' && $condition !== '') {
            return array(
                'condition' => $condition,
                'action'    => array(),
                'open'      => true,
            );
        }

        $this->error = 'Form is empty';

        return null;
    }
    
    /**
     * Add tax rate
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function doActionAddSubmit()
    {
        $node = $this->readTaxForm();
        
        if (is_null($node)) {
            $this->valid = false;
            $this->doActionAdd(); // show errors and the form again

        } else {
            $subTree =& $this->locateNode($this->taxes->_rates, $this->indexes);
            /*
            // TODO - check it
            if (isset($subTree['action'])) {
                $subTree['action'][] = $node;

            } else {
                $subTree[] = $node;
            }
            */
            $subTree = $node;

            // store
            $this->taxes->setSchema(
                array(
                    'tax_rates' => $this->taxes->_rates,
                )
            );

            if (\XLite\Core\Request::getInstance()->add_another) {
                $this->set('returnUrl', 'admin.php?target=taxes&page=add_rate&mode=add');
                $this->set('mode', 'add');

            } else {
                $this->set('mode', '');
            }
        }
    }

    /**
     * Edit tax rate
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function doActionEditSubmit()
    {
        $node = $this->readTaxForm();
        if (is_null($node)) {
            $this->valid = false;
            $this->doActionEdit(); // show errors and the form again

        } else {

            $subTree =& $this->locateNode($this->taxes->_rates, $this->indexes);
            if (empty($node['action']))    {
                $action = $subTree['action'];
            }

            $subTree = $node;
             if (!empty($action)) {
                $subTree['action'] = $action;
            }

            // store
            $this->taxes->setSchema(
                array(
                    'tax_rates' => $this->taxes->_rates,
                )
            );
            $this->set('mode', '');
        }
    }

    /**
     * Delete tax rate 
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function doActionDeleteRate()
    {
        $ind = $this->_levels[\XLite\Core\Request::getInstance()->ind];
        $subTreeIndex = $ind;
        $lastIndex = array_pop($subTreeIndex); // remove last
        $subTree =& $this->locateNode($this->taxes->_rates, $subTreeIndex);

        if (isset($subTree[$lastIndex])) {
            unset($subTree[$lastIndex]);
        }

        if (isset($subTree['action'][$lastIndex])) {
            unset($subTree['action'][$lastIndex]);
        }

        // store
        $this->taxes->setSchema(
            array(
                'tax_rates' => $this->taxes->_rates,
            )
        );

        $this->set('mode', '');

        $this->setReturnUrl($this->buildUrl('taxes', '', array('page' => 'rates')));
    }

    function getTaxCondParam($node, $param)
    {
        if (is_array($node)) {
            $cond = $node['condition'];
            $taxParams = $this->taxes->_parseCondition($cond);
            if (isset($taxParams[$param])) {
                return $taxParams[$param];
            }
        }
        return null;
    }
    
    function initRuleParams()
    {
        // Countries

        $countries = array(
            'name'   => 'Countries',
            'var'    => 'country',
            'cond'   => 'country',
            'values' => array(
                'EU country' => 'EU country',
            ),
        );

        $list = \XLite\Core\Database::getRepo('XLite\Model\Country')->findAll();
        foreach ($list as $country) {
            $countries['values'][$country->code] = $country->country;
        }
        unset($list);

        // States

        $states = array(
            'name'      => 'States',
            'var'       => 'state',
            'cond'      => 'state',
            'values'    => array(),
            'diplay_ex' => 1,
        );

        $list = \XLite\Core\Database::getRepo('\XLite\Model\State')->findAllStates();

        foreach ($list as $state) {
            if (!isset($states['values'][$state->country_code])) {
                $states['values'][$state->country_code] = array(
                    'country' => $state->country->country,
                    'states'  => array(),
                );
            }

            $states['values'][$state->country_code]['states'][$state->state_id] = $state->state;
        }

        // Cities

        $cities = array(
            'name'   => 'Cities',
            'var'    => 'city',
            'cond'   => 'city',
            'values' => array(),
        );
        $cities = \XLite\Core\Database::getRepo('XLite\Model\Address')->findAllCities();
        foreach ($cities as $c) {
            $cities['values'][$c] = $c;
        }
        if (isset($cities['values'][''])) {
            unset($cities['values']['']);
        }

        // Payment methods

        $pm = array(
            'name'   => 'Payment method',
            'var'    => 'pm',
            'cond'   => 'payment method',
            'values' => array(),
        );
        $methods = \XLite\Core\Database::getRepo('\XLite\Model\Payment\Method')->findAllActive();
        foreach ($methods as $method) {
            $pm['values'][$method->getName()] = $method->getName();
        }

        // Product classes
    
        $classes = array(
            'name'   => 'Product class, either new or existing',
            'var'    => 'pclass',
            'cond'   => 'product class',
            'values' => array_unique(array_merge(array('shipping service'), $this->taxes->getProductClasses())),
        );
        array_multisort($classes['values']);
        $classes['values'] = array_combine($classes['values'], $classes['values']);

        // Memberships

        $memberships = array(
            'name'   => 'User membership level',
            'var'    => 'membership',
            'cond'   => 'membership',
            'values' => array(
                0 => 'No membership',
            ),
        );

        foreach (\XLite\Core\Database::getRepo('\XLite\Model\Membership')->findActiveMemberships() as $m) {
            $memberships['values'][$m->membership_id] = $m->name;
        }

        // Postal zip codes

        $zips = array(
            'name'   => 'Zip codes/ranges (e.g. 43200-43300,55555)',
            'var'    => 'zip',
            'cond'   => 'zip',
            'values' => array(),
        );
        
        $this->taxParams = array(
            $countries,
            $states,
            $cities,
            $pm,
            $classes,
            $memberships,
            $zips,
        );

        $this->taxNames = $this->taxes->getTaxNames();
    }

    function _sortRates(&$rateTree, &$pos)
    {
        for ($i=0; $i<count($rateTree); $i++) {
            // sort children
            if (is_array($rateTree[$i]) && is_array($rateTree[$i]['action'])) {
                if (!isset($pos[$i])) {
                    continue;
                }
                $this->_sortRates($rateTree[$i]['action'], $pos[$i]);
            }
        }
        if (!isset($pos['orderbys']) || !is_array($pos['orderbys'])) {
            print "pos = "; print_r($pos);
            $this->doDie("pos['orderbys'] must be an array");
        }
        $ratesToSort = $rateTree;
        array_multisort($pos['orderbys'], $ratesToSort);
        $rateTree = $ratesToSort;
    }
    
    function &locateNode(&$tree, $path)
    {
        $ptr =& $tree;
        foreach ($path as $index) {
            if (isset($ptr['action'])) {
                $ptr =& $ptr['action'];
            }
            if (!isset($ptr[$index])) {
                // create a node 
                $ptr[$index] = array();
            }
            $ptr =& $ptr[$index];
        }
        return $ptr;
    }
    
    function _insertValue($expr, $value)
    {
        list($name,$oldval) = explode(':=', $expr);
        if (!isset($oldval)) {
            $this->doDie("expr=$expr - wrong format");
        }
        return "$name:=$value";
    }
    
    function getIndex($tax, $ind)
    {
        return (empty($tax['pos']) ? ($ind + 1) * 10 : $tax['pos']);
    }

    function getPath($ind)
    {
        return join(',', $this->_levels[$ind]);
    }

    function getTaxName($tax)
    {
        return $tax['name'];
    }
    
    function getRegistration($tax)
    {
        return isset($tax['registration']) ? $tax['registration'] : null;
    }
    
    function getNoteTaxName($node)
    {
        if (!isset(\XLite\Core\Request::getInstance()->taxName)) {
            if (is_array($node)) {
                $node = $node['action'];
                if (is_array($node)) {
                    return '';
                }
            }
            return $this->getVarName($node);
        } else {
            return \XLite\Core\Request::getInstance()->taxName;
        }
    }

    function getNoteTaxValue($node)
    {
        if (!isset(\XLite\Core\Request::getInstance()->taxValue)) {
            if (is_array($node)) {
                $node = $node['action'];
                if (is_array($node)) {
                    return '';
                }
            }
            return $this->getVarValue($node);
        } else {
            return \XLite\Core\Request::getInstance()->taxValue;
        }
    }

    function getVarName($expr)
    {
        list($name) = explode(':=', $expr);
        return $name;
    }

    function getCondVarName($expr)
    {
        $name = null;

        if (isset($expr['action']) && is_string($expr['action'])) {
            list($name) = explode(':=', $expr['action']);
        }

        return $name;
    }

    function getVarValue($expr)
    {
        $value = null;

        if (is_string($expr)) {
            list($name, $value) = explode(':=', $expr);
        }

        return $value;
    }

    function getCurrentVarValue($ind, $expr)
    {
        $postData = \XLite\Core\Request::getInstance()->getData();
        return (
            $this->isInvalidExp($ind) && isset($postData['varvalue']) && isset($postData['varvalue'][$ind]) 
            ? $postData['varvalue'][$ind]
            : $this->getVarValue($expr)
        );
    }

    function getCondVarValue($expr)
    {
        $expr = $expr['action'];
        list($name,$value) = explode(':=', $expr);
        return $value;
    }

    function getCurrentCondVarValue($ind, $expr)
    {
        $postData = \XLite\Core\Request::getInstance()->getData();
        return (
            $this->isInvalidExp($ind) && isset($postData['varvalue']) && isset($postData['varvalue'][$ind]) 
            ? $postData['varvalue'][$ind]
            : $this->getCondVarValue($expr)
        );
    }

    function getCondParam($node, $param, $name = null)
    {
        $result = '';

        if (
            $this->edit
            && $name
            && !isset(\XLite\Core\Request::getInstance()->$name)
        ) {

            if (is_array($node)) {
                $cond = $this->taxes->_parseCondition($node['condition']);
                if (isset($cond[$param])) {
                    $result = implode(',', $cond[$param]);
                }
            }

        } else {
            $result = \XLite\Core\Request::getInstance()->$name;
        }

        return $result;
    }

    function getDisplayName($tax)
    {
        return $tax['display_label'];
    }

    function getRates()
    {
        $ind = 0;
        $this->_rates = array();
        $this->_levels = array();
        $this->_maxLevel = 0;
        $this->_initRates($this->taxes->_rates, array(), $ind);
    }

    function _initRates($rates, $levels, &$ind)
    {
        if ($this->_maxLevel < count($levels)) {
            $this->_maxLevel = count($levels);
        }

        if (!is_array($rates)) {
            $this->_die ("rates='$rates' must be array");
        }

        foreach ($rates as $ind_rate => $rate) {
            $this->_rates[$ind] = $rate;
            $this->_levels[$ind] = $levels;
            $this->_levels[$ind][] = $ind_rate;
            $ind++;

            if (is_array($rate) && is_array($rate['action']) && isset($rate['open'])) {
                $levels1 = $levels;
                $levels1[] = $ind_rate;
                $this->_initRates($rate['action'], $levels1, $ind);
            }
        }
    }

    function isOpen($row)
    {
        return isset($row['open']);
    }

    function getLevels($ind)
    {
        return str_repeat(
            '<td width="35">&nbsp;</td>',
            count($this->_levels[$ind]) - 1
        );
    }

    /**
     * Display condition in human-readable style
     * 
     * @param array $cond Condition
     *  
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getCondition(array $cond)
    {
        $conditions = array();

        foreach ($this->taxes->_parseCondition($cond['condition']) as $name => $ids) {
            $condition = ucfirst($name);

            $method = 'convert' . \XLite\Core\Converter::convertToCamelCase($name) . 'Ids';
            if (method_exists($this, $method)) {
                $ids = $this->$method($ids);
            }

            if (1 < count($ids)) {
                $condition .= ' in (' . implode(' or ', $ids) . ')';

            } else {
                $condition .= ' = ' . array_shift($ids);
            }

            $conditions[] = $condition;
        }

        return implode(' and ', $conditions);
    }

    /**
     * Convert country id's to country names
     * 
     * @param array $ids Country id's
     *  
     * @return array
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function convertCountryIds(array $ids)
    {
        $qb = \XLite\Core\Database::getRepo('XLite\Model\Country')->createQueryBuilder();
        $keys = \XLite\Core\Database::buildInCondition($qb, $ids, 'id');
        $list = $qb->andWhere('c.code IN (' . implode(', ', $keys). ')')->getResult();

        $result = array();
        foreach ($list as $m) {
            $result[] = $m->country;
        }

        return $result;
    }

    /**
     * Convert state id's to state names
     * 
     * @param array $ids State id's
     *  
     * @return array
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function convertStateIds(array $ids)
    {
        $qb = \XLite\Core\Database::getRepo('XLite\Model\State')->createQueryBuilder();
        $keys = \XLite\Core\Database::buildInCondition($qb, $ids, 'id');
        $list = $qb->andWhere('s.state_id IN (' . implode(', ', $keys). ')')->getResult();

        $result = array();
        foreach ($list as $m) {
            $result[] = $m->getState();
        }

        return $result;
    }

    /**
     * Convert membership id's to membership names
     * 
     * @param array $ids Membership id's
     *  
     * @return array
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function convertMembershipIds(array $ids)
    {
        $qb = \XLite\Core\Database::getRepo('\XLite\Model\Membership')->createQueryBuilder();
        $keys = \XLite\Core\Database::buildInCondition($qb, $ids, 'id');
        $list = $qb->andWhere('m.membership_id IN (' . implode(', ', $keys). ')')->getResult();

        $result = array();
        foreach ($list as $m) {
            $result[] = $m->getName();
        }

        return $result;
    }

    function isAction($a) 
    {
        return is_scalar($a);
    }

    function isCondition($a)
    {
        return is_array($a) && is_array($a['action']);
    }

    function isConditionalAction($a)
    {
        return is_array($a) && is_scalar($a['action']);
    }

    function getColspan($ind, $additional=1)
    {
        return $this->_maxLevel-count($this->_levels[$ind])+$additional+1;
    }

    function getMaxColspan($additional=0)
    {
        return $this->_maxLevel+$additional;
    }

    function getTreePos($ind)
    {
        $levels = $this->_levels[$ind];
        return $levels[count($levels)-1]*10 + 10;
    }

    function getHeaderMargin()
    {
        $result = '';
        for ($i=0; $i<$this->_maxLevel; $i++) {
            $result .= "<th></th>";
        }
        return $result;
    }

    function isLast($ind)
    {
        return !isset($this->_levels[$ind+1]) || count($this->_levels[$ind+1]) < count($this->_levels[$ind]);
    }

    function action_calculator()
    {
        $postData = \XLite\Core\Request::getInstance()->getData();

        if (!empty($postData)) {
            $postData['country'] = $postData['billing_country'];
            unset($postData['billing_country']);
            $postData['state'] = $postData['billing_state'];
            unset($postData['billing_state']);
            $this->set('properties', $postData);

            $tax = new \XLite\Model\TaxRates();
            // setup tax rate calculator
            if (!is_array($tax->_conditionValues)) {
                $tax->_conditionValues = array();
            }
            foreach ($postData as $name => $value) {
                $name1 = str_replace('_', " ", $name);
                $tax->_conditionValues[$name1] = $this->$name;
            }
            if (isset($this->country)) {
                $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find($this->country);
                $tax->_conditionValues['country'] = $country->country;
                if ($country->eu_member) {
                    $tax->_conditionValues['country'] .= ",EU country";
                  }
            }
            if (isset($this->state)) {
                $state = \XLite\Core\Database::getRepo('XLite\Model\State')->find($this->state);
                $tax->_conditionValues['state'] = $state->state;
                unset($state);
            }

            // calculate taxes
            $tax->calculateTaxes();
            $this->item_taxes = $tax->_taxValues;
            foreach ($this->item_taxes as $taxkey => $taxvalue) {
                $this->item_taxes[$taxkey] = $tax->_calcFormula($taxvalue);
            }
            $tax->_conditionValues['product class'] = 'shipping service';
            $tax->calculateTaxes();
            $this->shipping_taxes = $tax->_taxValues;
            foreach ($this->shipping_taxes as $taxkey => $taxvalue) {
                $this->shipping_taxes[$taxkey] = $tax->_calcFormula($taxvalue);
            }
            $this->set('display_taxes', 1);
        }
        
        // show tax calculator
        $w = new \XLite\View\Controller();
        $w->component = $this;
        $w->set('template', "tax/calculator.tpl");
        $w->init();
        $w->display();
        // do not output anything
        $this->set('silent', true);
    }

    function isDoubleValue($value)
    {
        if (strcmp(strval(doubleval($value)), strval($value)) == 0)
        {
            return true;
        }
        return false;
    }

    function action_save()
    {
        $name = $this->get('save_schema');
        if ($name == "") {
            $name = $this->get('new_name');
        }

        $tax = new \XLite\Model\TaxRates();
        $tax->saveSchema($name);
    }

    function action_export()
    {
        $name = $this->get('export_schema');
        $tax = new \XLite\Model\TaxRates();
        $schema = $tax->get("predefinedSchemas.$name");
        if (!is_null($schema)) {
            $this->set('silent', true);
            $this->startDownload("$name.tax");
            print serialize($schema);
        }
    }

    function action_import()
    {
        if (!$this->checkUploadedFile()) {
            $this->set('valid', false);
            $this->set('invalid_file', true);
            return;
        }

        $file = $this->get('uploadedFile');
        if (is_null($file)) {
            return;
        }
        $name = basename($_FILES['userfile']['name'], ".tax");
        $schema = unserialize(file_get_contents($file));
        $tax = new \XLite\Model\TaxRates();
        $tax->saveSchema($name, $schema);
    }

    function getEdit()
    {
        return $this->get('mode') == "edit";
    }

    function getSchemas()
    {
        $schemas = unserialize($this->xlite->config->Taxes->schemas);
        return ($schemas ? $schemas : array());
    }

    function isInvalidExp($ind)
    {
        return isset($this->invalidExpressions[$ind]) || isset($this->invalidFormula[$ind]);
    }

    function getExpInvalidVar($ind)
    {
        return (isset($this->invalidExpressions[$ind]) ? $this->invalidExpressions[$ind] : '???');
    }

    function getInvalidFormula($ind)
    {
        return (isset($this->invalidFormula[$ind]) ? $this->invalidFormula[$ind] : '???');
    }
}
