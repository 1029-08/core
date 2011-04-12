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
 * @package    Tests
 * @subpackage Classes
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      1.0.0
 */

class XLite_Tests_Module_CDev_ProductOptions_Model_Product extends XLite_Tests_TestCase
{
    protected $product;

    protected $testGroup = array(
        'name'      => 'Test name',
        'fullname'  => 'Test full name',
        'orderby'   => 10,
        'type'      => XLite\Module\CDev\ProductOptions\Model\OptionGroup::GROUP_TYPE,
        'view_type' => XLite\Module\CDev\ProductOptions\Model\OptionGroup::SELECT_VISIBLE,
        'cols'      => 11,
        'rows'      => 12,
        'enabled'   => true,
    );

    protected $testOption = array(
        'name'      => 'Test option name',
        'orderby'   => 11,
        'enabled'   => true,
    );

    protected function setUp()
    {
        parent::setUp();

        \XLite\Core\Database::getEM()->clear();
    }

    public function testHasOptions()
    {
        $group = $this->getTestGroup();

        $this->assertTrue($this->getProduct()->hasOptions(), 'has options');

        \XLite\Core\Database::getEM()->remove($group);
        $this->getProduct()->getOptionGroups()->clear();

        \XLite\Core\Database::getEM()->flush();

        $this->assertFalse($this->getProduct()->hasOptions(), 'has not options');
    }

    public function testGetActiveOptions()
    {
        $group = $this->getTestGroup();

        $list = $this->getProduct()->getActiveOptions();

        $this->assertEquals(1, count($list), 'check list count');

        $this->assertEquals($group->getGroupId(), $list[0]->getGroupId(), 'check id');

        $group->setEnabled(false);
        \XLite\Core\Database::getEM()->persist($group);
        \XLite\Core\Database::getEM()->flush();

        $list = $this->getProduct()->getActiveOptions();

        $this->assertEquals(1, count($list), 'check list count #2 (cache)');

        $id = $this->getProduct()->getProductId();
        $this->product = null;
        \XLite\Core\Database::getEM()->clear();

        $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($id);
        $list = $product->getActiveOptions();

        $this->assertEquals(0, count($list), 'check list count (empty)');
    }

    public function testIsDisplayPriceModifier()
    {
        $this->assertTrue($this->getProduct()->isDisplayPriceModifier(), 'always true');
    }

    public function testPrepareOptions()
    {
        $group = $this->getTestGroup();

        $ids = array(
            $group->getGroupId() => $group->getOptions()->get(0)->getOptionId(),
        );

        $data = $this->getProduct()->prepareOptions($ids);
        $this->assertEquals(1, count($data), 'check count');
        $this->assertTrue(isset($data[$group->getGroupId()]), 'prepare options (group id)');
        $this->assertEquals($data[$group->getGroupId()]['option']->getOptionId(), $group->getOptions()->get(0)->getOptionId(), 'prepare options (option id)');
        $this->assertEquals($data[$group->getGroupId()]['value'], $group->getOptions()->get(0)->getOptionId(), 'prepare options (option id)');

        $ids = array(
            $group->getGroupId() => -1,
        );

        $this->assertNull($this->getProduct()->prepareOptions($ids), 'wrong option id');

        $ids = array(
            -1 => $group->getOptions()->get(0)->getOptionId(),
        );

        $this->assertNull($this->getProduct()->prepareOptions($ids), 'wrong group id');

        $g2 = new XLite\Module\CDev\ProductOptions\Model\OptionGroup();

        $g2->setProduct($this->getProduct());
        $this->getProduct()->addOptionGroups($g2);

        $g2->map($this->testGroup);
        $g2->setType('t');

        \XLite\Core\Database::getEM()->persist($g2);
        \XLite\Core\Database::getEM()->flush();

        $ids = array(
            $group->getGroupId() => $group->getOptions()->get(0)->getOptionId(),
            $g2->getGroupId()    => 'test',
        );

        $data = $this->getProduct()->prepareOptions($ids);
        $this->assertEquals(2, count($data), 'check count #2');
        $this->assertTrue(isset($data[$group->getGroupId()]), 'prepare options (group id) #2');
        $this->assertEquals($data[$group->getGroupId()]['option']->getOptionId(), $group->getOptions()->get(0)->getOptionId(), 'prepare options (option id) #2');
        $this->assertEquals($data[$group->getGroupId()]['value'], $group->getOptions()->get(0)->getOptionId(), 'prepare options (option id) #2');

        $this->assertTrue(isset($data[$g2->getGroupId()]), 'prepare options (group id) #3');
        $this->assertNull($data[$g2->getGroupId()]['option'], 'prepare options (option id) #3');
        $this->assertEquals('test', $data[$g2->getGroupId()]['value'], 'prepare options (option id) #3');

        $ids = array(
            $group->getGroupId() => $group->getOptions()->get(0)->getOptionId(),
        );

        $id = $this->getProduct()->getProductId();
        $this->product = null;
        \XLite\Core\Database::getEM()->clear();

        $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($id);

        $data = $product->prepareOptions($ids);

        $this->assertEquals(2, count($data), 'check count #3');
        $this->assertEquals('', $data[$g2->getGroupId()]['value'], 'prepare options (option id) #4');
    }

    public function testGetDefaultProductOptions()
    {
        $group = $this->getTestGroup();

        $data = $this->getProduct()->getDefaultProductOptions();
        $this->assertTrue(isset($data[$group->getGroupId()]), 'prepare options (group id)');
        $this->assertEquals($data[$group->getGroupId()]['option']->getOptionId(), $group->getOptions()->get(0)->getOptionId(), 'prepare options (option id)');
        $this->assertEquals($data[$group->getGroupId()]['value'], $group->getOptions()->get(0)->getOptionId(), 'prepare options (value)');

        $e = new XLite\Module\CDev\ProductOptions\Model\OptionException();
        $e->setOption($group->getOptions()->get(0));
        $e->setExceptionId(
            \XLite\Core\Database::getRepo('XLite\Module\CDev\ProductOptions\Model\OptionException')
            ->getNextExceptionId()
        );

        $group->getOptions()->get(0)->addExceptions($e);

        \XLite\Core\Database::getEM()->persist($group);
        \XLite\Core\Database::getEM()->flush();

        $data = $this->getProduct()->getDefaultProductOptions();

        $this->assertTrue(isset($data[$group->getGroupId()]), 'prepare options (group id) #2');
        $this->assertEquals($data[$group->getGroupId()]['option']->getOptionId(), $group->getOptions()->get(1)->getOptionId(), 'prepare options (option id) #2');
        $this->assertEquals($data[$group->getGroupId()]['value'], $group->getOptions()->get(1)->getOptionId(), 'prepare options (value) #2');
    }

    public function testCheckOptionsException()
    {
        $group = $this->getTestGroup();

        $data = array(
            $group->getGroupId() => array(
                'option' => $group->getOptions()->get(0),
                'value'  => $group->getOptions()->get(0)->getOptionId(),
            ),
        );

        $this->assertTrue($this->getProduct()->checkOptionsException($data), 'check options (passed)');

        $data = array(
            $group->getGroupId() => array(
                'option' => $group->getOptions()->get(2),
                'value'  => $group->getOptions()->get(2)->getOptionId(),
            ),
        );

        $this->assertFalse($this->getProduct()->checkOptionsException($data), 'check options (failed)');
    }

    protected function getProduct()
    {
        if (!isset($this->product)) {
            $list = \XLite\Core\Database::getRepo('XLite\Model\Product')->findFrame(1, 1);

            $this->product = array_shift($list);
            foreach ($list as $p) {
                $p->detach();
            }

            if (!$this->product) {
                $this->fail('Can not find enabled product');
            }

            // Clean option groups
            foreach ($this->product->getOptionGroups() as $group) {
                \XLite\Core\Database::getEM()->remove($group);
            }
            $this->product->getOptionGroups()->clear();
            \XLite\Core\Database::getEM()->flush();
        }

        return $this->product;
    }

    protected function getTestGroup()
    {
        $group = new XLite\Module\CDev\ProductOptions\Model\OptionGroup();

        $group->setProduct($this->getProduct());
        $this->getProduct()->addOptionGroups($group);

        $group->map($this->testGroup);

        $option = new XLite\Module\CDev\ProductOptions\Model\Option();
        $option->setGroup($group);
        $group->addOptions($option);

        $option->map($this->testOption);

        $option = new XLite\Module\CDev\ProductOptions\Model\Option();
        $option->setGroup($group);
        $group->addOptions($option);

        $option->map($this->testOption);
        $option->setName('o2');

        $option = new XLite\Module\CDev\ProductOptions\Model\Option();
        $option->setGroup($group);
        $group->addOptions($option);

        $option->map($this->testOption);
        $option->setName('o3');

        $s = new XLite\Module\CDev\ProductOptions\Model\OptionSurcharge();
        $s->setOption($option);
        $s->setType('price');
        $s->setModifier(10);
        $s->setModifierType('$');

        $option->addSurcharges($s);

        $e = new XLite\Module\CDev\ProductOptions\Model\OptionException();
        $e->setOption($option);
        $e->setExceptionId(
            \XLite\Core\Database::getRepo('XLite\Module\CDev\ProductOptions\Model\OptionException')
            ->getNextExceptionId()
        );

        $option->addExceptions($e);
        
        \XLite\Core\Database::getEM()->persist($group);
        \XLite\Core\Database::getEM()->flush();

        return $group;
    }
}
