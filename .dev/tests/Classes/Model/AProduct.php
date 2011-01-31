<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * XLite\Model\Repo\Product class tests
 *
 * @category   LiteCommerce
 * @package    Tests
 * @subpackage Classes
 * @author     Creative Development LLC <info@cdev.ru>
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

abstract class XLite_Tests_Model_AProduct extends XLite_Tests_TestCase
{
    /**
     * setUp
     *
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function setUp()
    {
        parent::setUp();

        \XLite\Core\Database::getEM()->clear();

        $this->query(file_get_contents(__DIR__ . '/Repo/sql/product/setup.sql'));
        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * tearDown
     *
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->query(file_get_contents(__DIR__ . '/Repo/sql/product/restore.sql'));
        \XLite\Core\Database::getEM()->flush();
    }
}
