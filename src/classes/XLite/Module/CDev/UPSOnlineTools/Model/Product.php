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
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Module\CDev\UPSOnlineTools\Model;

/**
 * ____description____
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    public function __construct($id=null)
    {
        parent::__construct($id);
        $this->fields['ups_width'] = 1;
        $this->fields['ups_height'] = 1;
        $this->fields['ups_length'] = 1;
        $this->fields['ups_handle_care'] = 0;
        $this->fields['ups_add_handling'] = 0;
        $this->fields['ups_declared_value'] = 0;
        $this->fields['ups_declared_value_set'] = 0;
        $this->fields['ups_packaging'] = 0;
    }

    function getDeclaredValue()
    {
        return ($this->get('ups_declared_value_set')) ? $this->get('ups_declared_value') : $this->get('price');
    }

    function getWeightConv($unit)
    {
        require_once LC_MODULES_DIR . 'UPSOnlineTools' . LC_DS . 'encoded.php';
        return UPSOnlineTools_convertWeight($this->get('weight'), $this->config->General->weight_unit, $unit, 2);
    }

}
