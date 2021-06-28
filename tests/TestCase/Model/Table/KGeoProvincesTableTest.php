<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KGeoProvincesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KGeoProvincesTable Test Case
 */
class KGeoProvincesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KGeoProvincesTable
     */
    public $KGeoProvinces;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KGeoProvinces',
        'app.GeoRegions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KGeoProvinces') ? [] : ['className' => KGeoProvincesTable::class];
        $this->KGeoProvinces = TableRegistry::getTableLocator()->get('KGeoProvinces', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KGeoProvinces);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
