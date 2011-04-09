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

namespace XLite\Model\Repo\Shipping;

/**
 * Shipping method model
 * 
 * @see   ____class_see____
 * @since 1.0.0
 */
class Method extends \XLite\Model\Repo\ARepo
{
    /**
     * Repository type 
     * 
     * @var   string
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $type = self::TYPE_SECONDARY;

    /**
     * Alternative record identifiers
     *
     * @var   array
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $alternativeIdentifier = array(
        array('processor', 'code'),
    );


    /**
     * Returns shipping methods by specified processor Id 
     * 
     * @param string $processorId Processor Id
     *  
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function findMethodsByProcessor($processorId)
    {
        return $this->defineFindMethodsByProcessor($processorId)->getResult();
    }

    /**
     * Returns shipping methods by ids
     * 
     * @param array $ids Array of method_id values
     *  
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function findMethodsByIds($ids)
    {
        return $this->defineFindMethodsByIds($ids)->getResult();
    }


    /**
     * Adds additional condition to the query for checking if method is enabled
     * 
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder object
     * @param string                     $alias Entity alias OPTIONAL
     *  
     * @return \Doctrine\ORM\QueryBuilder
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function addEnabledCondition(\Doctrine\ORM\QueryBuilder $qb, $alias = 'm')
    {
        if (!\XLite::getInstance()->isAdminZone()) {
            $qb->andWhere($alias . '.enabled = 1');
        }

        return $qb;
    }

    /**
     * Define query builder object for findMethodsByProcessor()
     * 
     * @param string $processorId Processor Id
     *  
     * @return \Doctrine\ORM\QueryBuilder
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineFindMethodsByProcessor($processorId)
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.processor =:processorId')
            ->setParameter('processorId', $processorId);

        return $this->addEnabledCondition($qb);
    }

    /**
     * Define query builder object for findMethodsByIds()
     * 
     * @param array $ids Array of method_id values
     *  
     * @return \Doctrine\ORM\QueryBuilder
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineFindMethodsByIds($ids)
    {
        $qb = $this->createQueryBuilder('m');

        return $qb->andWhere($qb->expr()->in('m.method_id', $ids));
    }
}
