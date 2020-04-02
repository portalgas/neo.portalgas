<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KDeliveriesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KDeliveriesTable Test Case
 */
class KDeliveriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KDeliveriesTable
     */
    public $KDeliveries;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KDeliveries',
        'app.Organizations',
        'app.GcalendarEvents',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KDeliveries') ? [] : ['className' => KDeliveriesTable::class];
        $this->KDeliveries = TableRegistry::getTableLocator()->get('KDeliveries', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KDeliveries);

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
