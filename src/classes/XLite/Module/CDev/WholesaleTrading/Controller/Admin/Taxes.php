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

namespace XLite\Module\CDev\WholesaleTrading\Controller\Admin;

/**
 * Taxes
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class Taxes extends \XLite\Controller\Admin\Taxes implements \XLite\Base\IDecorator
{
    /*
     * This function required for configuring discounts taxing policy
     */
    function isDiscountUsedForTaxes()
    {
        return true;
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
        parent::doActionUpdateOptions();

        \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption(
            array(
                'category' => 'Taxes',
                'name'     => 'discounts_after_taxes',
                'value'    => \XLite\Core\Request::getInstance()->discounts_after_taxes
            )
        );
    }
}
