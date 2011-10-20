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

namespace XLite\Model\Repo;

/**
 * Language repository
 *
 * @see   ____class_see____
 * @since 1.0.0
 */
class Language extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Repository type
     *
     * @var   string
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $type = self::TYPE_SERVICE;

    /**
     * Default 'order by' field name
     *
     * @var   string
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $defaultOrderBy = 'code';

    /**
     * Global default language (cache)
     *
     * @var   \XLite\Model\Language
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $defaultLanguage = null;

    // {{{

    /**
     * Get languages query
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getLanguagesQuery()
    {
        $query = array(
            \XLite\Core\Config::getInstance()->General->defaultLanguage->getCode(),
            $this->getDefaultLanguage()->getCode(),
        );

        return array_unique($query);
    }

    /**
     * Get global default language
     *
     * @return string Language code
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getDefaultLanguage()
    {
        if (!isset($this->defaultLanguage)) {
            $this->defaultLanguage = \XLite\Core\Database::getRepo('\XLite\Model\Language')
                ->findOneByCode('en');

            if (!$this->defaultLanguage) {
                // TODO - add throw exception
            }
        }

        return $this->defaultLanguage;
    }

    // }}}

    // {{{ defineCacheCells

    /**
     * Define cache cells
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineCacheCells()
    {
        $list = parent::defineCacheCells();

        $list['all'] = array();

        $list['status'] = array(
            self::ATTRS_CACHE_CELL => array('status'),
        );

        return $list;
    }

    // }}}

    // {{{ findAllLanguages

    /**
     * Find all languages
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function findAllLanguages()
    {
        $data = $this->getFromCache('all');
        if (!isset($data)) {
            $data = $this->defineAllLanguagesQuery()->getResult();
            $this->saveToCache($data, 'all');
        }

        return $data;
    }

    /**
     * Define query builder for findAllLanguages()
     *
     * @return \Doctrine\ORM\QueryBuilder
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineAllLanguagesQuery()
    {
        return $this->createQueryBuilder();
    }

    // }}}

    // {{{ findActiveLanguages

    /**
     * Find all active languages
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function findActiveLanguages()
    {
        $data = $this->getFromCache('status', array('status' => \XLite\Model\Language::ENABLED));
        if (!isset($data)) {
            $data = $this->defineByStatusQuery(\XLite\Model\Language::ENABLED)->getResult();
            $this->saveToCache($data, 'status', array('status' => \XLite\Model\Language::ENABLED));
        }

        return $data;
    }

    /**
     * Find all inactive languages
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function findInactiveLanguages()
    {
        return $this->defineByStatusQuery(\XLite\Model\Language::INACTIVE)->getResult();
    }

    /**
     * Define query builder for findActiveLanguages()
     *
     * @param integer $status Status key
     *
     * @return \Doctrine\ORM\QueryBuilder
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineByStatusQuery($status)
    {
        return $this->createQueryBuilder()
            ->andWhere('l.status = :status')
            ->setParameter('status', $status);
    }

    // }}}

    // {{{ findAddedLanguages

    /**
     * Find all added languages
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function findAddedLanguages()
    {
        return $this->defineAddedQuery()->getResult();
    }

    /**
     * Define query builder for findAddedLanguages()
     *
     * @return \Doctrine\ORM\QueryBuilder
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineAddedQuery()
    {
        return $this->createQueryBuilder()
            ->andWhere('l.status != :status')
            ->setParameter('status', \XLite\Model\Language::INACTIVE);
    }

    // }}}

    // {{{ findOneByCode

    /**
     * Find language one by code
     *
     * @param string $code Code
     *
     * @return \XLite\Model\Language|void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function findOneByCode($code)
    {
        return $this->defineOneByCodeQuery($code)->getSingleResult();
    }

    /**
     * Define query builder for findOneByCode()
     *
     * @param string $code Language code
     *
     * @return \Doctrine\ORM\QueryBuilder
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineOneByCodeQuery($code)
    {
        return $this->createQueryBuilder()
            ->andWhere('l.code = :code')
            ->setParameter('code', $code)
            ->setMaxResults(1);
    }

    // }}}
}
