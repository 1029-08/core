<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * XLite\Model\Repo\Zone class tests
 *
 * @category   LiteCommerce
 * @package    Tests
 * @subpackage Classes
 * @author     Creative Development LLC <info@cdev.ru>
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      1.0.0
 */

class XLite_Tests_Model_Repo_Zone extends XLite_Tests_TestCase
{
	/**
	 * testGetApplicableZones
	 *
	 * @return void
	 * @access public
	 * @see    ____func_see____
	 * @since  1.0.0
	 */
	public function testGetApplicableZones()
	{
        $data = array();
        $data[] = array(
            'address'  => array(
                'country' => 'US',
                'state'   => 'NY',
                'city'    => 'New York',
                'zipcode' => '10134',
                'address' => 'Some address',
            ),
            'result' => array(
                0x01 + 0x02 + 0x08 + 0x10 => 'New York area',
                0x01                      => 'United States area',
                0                         => 'Default zone (all addresses)',
            )
        );

        $data[] = array(
            'address'  => array(
                'country' => 'US',
                'state'   => 'NY',
                'city'    => 'New Worker',
                'zipcode' => '10134',
                'address' => '92nd Street Y 1395 Lexington Avenue',
            ),
            'result' => array(
                0x01 + 0x02 + 0x08 => 'New York area',
                0x01               => 'United States area',
                0                  => 'Default zone (all addresses)',
            )
        );

        $data[] = array(
            'address'  => array(
                'country' => 'US',
                'state'   => 'CA',
                'city'    => 'New York',
                'zipcode' => '10134',
                'address' => 'Some address',
            ),
            'result' => array(
                0x01 + 0x02 => 'California area',
                0x01        => 'United States area',
                0           => 'Default zone (all addresses)'
            )
        );

        $data[] = array(
            'address'  => array(
                'country' => 'FR',
                'state'   => 'NY',
                'city'    => 'New York',
                'zipcode' => '10134',
                'address' => 'Some address',
            ),
            'result' => array(
                0x01 => 'Europe',
                0    => 'Default zone (all addresses)',
            )
        );

        $data[] = array(
            'address'  => array(
                'country' => 'FR',
                'state'   => 'NY',
                'city'    => 'New York',
                'zipcode' => '10134',
                'address' => 'Some address',
            ),
            'result' => array(
                0x01 => 'Europe',
                0    => 'Default zone (all addresses)',
            )
        );

        $data[] = array(
            'address'  => array(
                'country' => 'FR',
                'state'   => 34,
                'city'    => 'New York',
                'zipcode' => '10134',
                'address' => 'Some address',
            ),
            'result' => array(
                0x01 => 'Europe',
                0    => 'Default zone (all addresses)',
            )
        );


        foreach ($data as $i => $dt) {

            $applicableZones = \XLite\Core\Database::getRepo('XLite\Model\Zone')->findApplicableZones($dt['address']);

            $this->assertTrue(is_array($applicableZones), 'getApplicableZones() must return an array');

            $result = array();

            foreach ($applicableZones as $weight => $zone) {
                $this->assertTrue($zone instanceof \XLite\Model\Zone, 'getApplicableZones() must return an array of \XLite\Model\Zone instances');
                $result[$weight] = $zone->getZoneName();
            }

            $this->assertEquals($dt['result'], $result, 'check ' . $i . ' iteration');
        }
    }

    /**
     * testCleanCache
     *
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function testCleanCache()
    {
        $cacheDriver = \XLite\Core\Database::getCacheDriver();

        // Test cleanCache() with parameter
        $cacheDriver->save('Model_Zone.data.all', array('test'));
        $cacheDriver->save('Model_Zone.data.zone.33000.testcase', array('test'));
        $cacheDriver->save('Model_Zone.data.zone.34000.testcase', array('test'));

        \XLite\Core\Database::getRepo('XLite\Model\Zone')->cleanCache(33000);

        $this->assertFalse($cacheDriver->contains('Model_Zone.data.all'), 'Model_Zone.all record was not removed');
        $this->assertFalse($cacheDriver->contains('Model_Zone.data.zone.33000.testcase'), 'Model_Zone.zone.33000.testcase record was not removed');
        $this->assertTrue($cacheDriver->contains('Model_Zone.data.zone.34000.testcase'), 'Model_Zone.zone.34000.testcase record was removed');

        \XLite\Core\Database::getRepo('XLite\Model\Zone')->cleanCache(34000);

        $this->assertFalse($cacheDriver->contains('Model_Zone.data.zone.34000.testcase'), 'Model_Zone.zone.34000.testcase record was not removed');

        // Test cleanCache() without parameter
        $cacheDriver->save('Model_Zone.data.all', array('test'));
        $cacheDriver->save('Model_Zone.data.zone.33000.testcase', array('test'));
        $cacheDriver->save('Model_Zone.data.zone.34000.testcase', array('test'));

        \XLite\Core\Database::getRepo('XLite\Model\Zone')->cleanCache();

        $this->assertFalse($cacheDriver->contains('Model_Zone.data.all'), 'Model_Zone.all record was not removed');
        $this->assertFalse($cacheDriver->contains('Model_Zone.data.zone.33000.testcase'), 'Model_Zone.zone.33000.testcase record was not removed');
        $this->assertFalse($cacheDriver->contains('Model_Zone.data.zone.34000.testcase'), 'Model_Zone.zone.34000.testcase record was not removed');
    }

    /**
     * testFindAllZones
     *
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function testFindAllZones()
    {
        $zones = \XLite\Core\Database::getRepo('XLite\Model\Zone')->findAllZones();

        $this->assertTrue(is_array($zones), 'findAllZones() must return an array');

        foreach ($zones as $zone) {
            $this->assertTrue($zone instanceof \XLite\Model\Zone, 'findAllZones() must return an array of \XLite\Model\Zone instances');
        }
    }

    /**
     * testFindZone
     *
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function testFindZone()
    {
        $zone = \XLite\Core\Database::getRepo('XLite\Model\Zone')->findOneBy(array('zone_name' => 'New York area'));
        $this->assertTrue($zone instanceof \XLite\Model\Zone, 'check zone');

        $id = $zone->getZoneId();

        \XLite\Core\Database::getEM()->clear();
        $zone = \XLite\Core\Database::getRepo('XLite\Model\Zone')->findZone($id);
        $this->assertTrue($zone instanceof \XLite\Model\Zone, 'findZone(20) must return an instance of \XLite\Model\Zone');


        $zone = \XLite\Core\Database::getRepo('XLite\Model\Zone')->findZone(20000);
        $this->assertNull($zone, 'findZone(20000) must return null');
    }

}
