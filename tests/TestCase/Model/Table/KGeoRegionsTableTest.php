<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KGeoRegionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KGeoRegionsTable Test Case
 */
class KGeoRegionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KGeoRegionsTable
     */
    public $KGeoRegions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KGeoRegions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KGeoRegions') ? [] : ['className' => KGeoRegionsTable::class];
        $this->KGeoRegions = TableRegistry::getTableLocator()->get('KGeoRegions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KGeoRegions);

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
}
