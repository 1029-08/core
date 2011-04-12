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
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      1.0.0
 */

namespace XLite\Module\CDev\DrupalConnector\Controller;

/**
 * Abstract controller 
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   1.0.0
 */
abstract class AController extends \XLite\Controller\AController implements \XLite\Base\IDecorator
{
    /**
     * Return Viewer object
     * 
     * @return \XLite\View\Controller
     * @access public
     * @since  1.0.0
     */
    public function getViewer()
    {
        $viewer = parent::getViewer();

        if (
            $this->isAJAXViewer()
            && \XLite\Core\Request::getInstance()->widgetConfId
            && \XLite\Module\CDev\DrupalConnector\Handler::isCMSStarted()
        ) {
            $data = \XLite\Module\CDev\DrupalConnector\Drupal\Model::getInstance()
                ->getBlock(\XLite\Core\Request::getInstance()->widgetConfId);

            if ($data && isset($data['options']) && is_array($data['options'])) {
                $viewer->setWidgetParams($data['options']);
            }
        }

        return $viewer;
    }

    /**
     * Argument convertion: <DRUPAL> --> <LC>
     *
     * @param string $path     Portal path
     * @param array  $args     Druapl URL arguments
     * @param array  $pageArgs LC-specific URL arguments
     *
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public static function getPortalLCArgs($path, array $args = array(), array $pageArgs = array())
    {
        return array('target' => static::getTargetByClassName()) + $pageArgs;
    }

    /**
     * Argument convertion: <LC> --> <DRUPAL>
     *
     * @param string $path Drupal path
     * @param array  $args LC URL arguments
     *
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public static function getPortalDrupalArgs($path, array $args = array())
    {
        return $path . (empty($args) ? '' : ('/' . \Includes\Utils\Converter::buildQuery($args, '-', '/')));
    }
}
