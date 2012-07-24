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

namespace Includes\Utils;

/**
 * FileFilter
 *
 * @package    XLite
 * @see        ____class_see____
 * @since      1.0.0
 */
class FileFilter extends \Includes\Utils\AUtils
{
    /**
     * Directory to iterate over
     *
     * @var    string
     * @access protected
     * @see    ____var_see____
     * @since  1.0.0
     */
    protected $dir;

    /**
     * Pattern to filter files by path
     *
     * @var    string
     * @access protected
     * @see    ____var_see____
     * @since  1.0.0
     */
    protected $pattern;

    /**
     * Mode
     *
     * @var    int
     * @access protected
     * @see    ____var_see____
     * @since  1.0.0
     */
    protected $mode;

    /**
     * Cache
     *
     * @var    \Includes\Utils\FileFilter\FilterIterator
     * @access protected
     * @see    ____var_see____
     * @since  1.0.0
     */
    protected $iterator;


    /**
     * Return the directory iterator
     *
     * @return \RecursiveIteratorIterator
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getUnfilteredIterator()
    {
        return new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->dir),
            $this->mode,
            \FilesystemIterator::SKIP_DOTS
        );
    }


    /**
     * Return the directory iterator
     *
     * @return \Includes\Utils\FileFilter\FilterIterator
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getIterator()
    {
        if (!isset($this->iterator)) {
            $this->iterator = new \Includes\Utils\FileFilter\FilterIterator(static::getUnfilteredIterator(), $this->pattern);
        }

        return $this->iterator;
    }

    /**
     * Constructor
     *
     * @param string $dir     Directory to iterate over
     * @param string $pattern Pattern to filter files
     * @param int    $mode    Filtering mode OPTIONAL
     *
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function __construct($dir, $pattern = null, $mode = \RecursiveIteratorIterator::LEAVES_ONLY)
    {
        $canonicalDir = \Includes\Utils\FileManager::getCanonicalDir($dir);

        if (empty($canonicalDir)) {
            \Includes\ErrorHandler::fireError('Path "' . $dir . '" is not exists or is not readable.');
        }

        $this->dir     = $canonicalDir;
        $this->pattern = $pattern;
        $this->mode    = $mode;
    }
}
