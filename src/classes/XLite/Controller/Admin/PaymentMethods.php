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
 * @copyright Copyright (c) 2011-2012 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 */

namespace XLite\Controller\Admin;

/**
 * Payment methods
 *
 */
class PaymentMethods extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Payment methods';
    }

    /**
     * Update payment methods
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $data = \XLite\Core\Request::getInstance()->data;

        if (!is_array($data)) {
            // TODO - add top message

        } else {

            $methods = array();

            foreach ($data as $id => $row) {
                $m = \XLite\Core\Database::getRepo('\XLite\Model\Payment\Method')->find($id);

                if ($m) {
                    $m->setName($row['name']);
                    $m->setDescription($row['description']);
                    $m->setOrderby(intval($row['orderby']));
                    $m->setEnabled(isset($row['enabled']) && '1' == $row['enabled']);

                    $methods[] = $m;

                } else {
                    // TODO - add top message
                }
            }

            if (!empty($methods)) {
                \XLite\Core\Database::getRepo('\XLite\Model\Payment\Method')->updateInBatch($methods);
                \XLite\Core\TopMessage::addInfo('Payment methods have been updated');
            }
        }
    }
}
