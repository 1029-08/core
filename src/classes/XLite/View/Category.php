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

namespace XLite\View;

/**
 * Category widget
 *
 * @see   ____class_see____
 * @since 1.0.0
 *
 * @ListChild (list="center", zone="customer")
 */
class Category extends \XLite\View\AView
{
    /**
     * WEB LC root postprocessing constant
     */
    const WEB_LC_ROOT = '{{WEB_LC_ROOT}}';

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'category';
        $result[] = 'main';

        return $result;
    }

    /**
     * Return widget default template
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getDefaultTemplate()
    {
        return 'category_description.tpl';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getCategory()->getDescription();
    }

    /**
     * Return description with postprocessing WEB LC root constant
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getDescription()
    {
        return str_replace(
            $this->getWebPreprocessingTags(),
            $this->getWebPreprocessingURL(),
            $this->getCategory()->getDescription()
        );
    }

    /**
     * Register tags to be replaced with some URLs
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getWebPreprocessingTags()
    {
        return array(
            static::WEB_LC_ROOT,
        );
    }

    /**
     * Register URLs that should be given instead of tags
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getWebPreprocessingURL()
    {
        // Get URL of shop. If the HTTPS is used then it should be cleaned from ?xid=<xid> construction
        $url = \XLite::getInstance()->getShopURL(null, \XLite\Core\Request::getInstance()->isHTTPS());

        // We are cleaning URL from unnecessary here <xid> construction
        $url = preg_replace('/(\?.*)/', '', $url);

        return array(
            $url,
        );
    }
}
