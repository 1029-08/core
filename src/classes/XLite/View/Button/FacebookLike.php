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

namespace XLite\View\Button;

/**
 * Facebook Like button
 *
 * @see   ____class_see____
 * @since 1.0.0
 *
 * @ListChild (list="socialbar.info", weight="100")
 */
class FacebookLike extends \XLite\View\AView
{
    /**
     * Widget parameters
     */
    const PARAM_WIDTH  = 'width';

    /**
     * Register JS files
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'button/js/facebook_like.js';

        return $list;
    }

    /**
     * Get width
     *
     * @return integer
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getWidth()
    {
        return $this->getParam(self::PARAM_WIDTH);
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
        return 'button/facebook_like.tpl';
    }

    /**
     * Define widget parameters
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_WIDTH => new \XLite\Model\WidgetParam\Int('Width', $this->getDefaultWidth()),
        );

    }

    /**
     * Get defaul width
     *
     * @return integer
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getDefaultWidth()
    {
        return 450;
    }

    /**
     * Get button attributes
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.15
     */
    protected function getButtonAttributes()
    {
        return array(
            'send'       => 'false',
            'width'      => $this->getWidth(),
            'show-faces' => 'true',
        );
    }
}
