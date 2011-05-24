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
 * @subpackage Includes
 * @author     Creative Development LLC <info@cdev.ru>
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      1.0.0
 */

namespace Includes\Decorator\Plugin\Templates\Plugin\Compiler;

/**
 * Decorator plugin to compile templates
 *
 * @package XLite
 * @see     ____class_see____
 * @since   1.0.0
 */
class Main extends \Includes\Decorator\Plugin\Templates\Plugin\APlugin
{
    /**
     * Instance of the Flexy compiler
     *
     * @var    \Xlite\Core\FlexyCompiler
     * @access protected
     * @see    ____var_see____
     * @since  1.0.0
     */
    protected $flexy;


    /**
     * Execute "postprocess" hook handler
     *
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function executeHookHandlerStepThird()
    {
        LC_DEVELOPER_MODE ?: $this->createTemplatesCache();
    }

    /**
     * Constructor
     *
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function __construct()
    {
        $this->flexy = \Xlite\Core\FlexyCompiler::getInstance();
    }

    /**
     * Static templates compilation
     *
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function createTemplatesCache()
    {
        foreach ($this->getAnnotatedTemplates() as $data) {
            $this->flexy->prepare($data['path'], true);
        }
    }
}
