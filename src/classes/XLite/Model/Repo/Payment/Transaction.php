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
 * @link      http://www.litecommerce.com/
 * @see       ____file_see____
 * @since     1.0.0
 */

namespace XLite\Model\Repo\Payment;

/**
 * Payment transaction
 *
 * @see   ____class_see____
 * @since 1.0.0
 */
class Transaction extends \XLite\Model\Repo\ARepo
{
    /**
     * Find transaction by data record and order id
     *
     * @param array   $params  Parameters_
     * @param integer $orderId Order id OPTIONAL
     *
     * @return \XLite\Model\Payment\Transaction
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function findOneByParams(array $params, $orderId = null)
    {
        return $this->defineOneByParamsQuery($params, $orderId)->getSingleResult();
    }

    /**
     * Define query for findOneByParams() method
     *
     * @param array   $params  Parameters
     * @param integer $orderId Order id
     *
     * @return \Doctrine\ORM\QueryBuilder
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineOneByParamsQuery(array $params, $orderId)
    {
        $qb = $this->createQueryBuilder()
            ->addSelect('COUNT(d.record_id) cnt')
            ->innerJoin('t.data', 'd')
            ->having('cnt = :cnt')
            ->setPrameter('cnt', count($params))
            ->setMaxResults(1);

        if (isset($orderId)) {
            $qb->innerJoin('t.order', 'o')
                ->addWhere('o.order_id = :orderId')
                ->setPrameter('orderId', $orderId);
        }

        $i = 1;
        foreach ($params as $name => $value) {
            $qb->andWhere('d.name = :name' . $i . ' AND d.value = :value' . $i)
                ->setParameter('name' . $i, $name)
                ->setParameter('value' . $i, $value);
            $i++;
        }

        return $qb;
    }
}
