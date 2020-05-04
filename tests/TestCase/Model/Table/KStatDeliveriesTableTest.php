<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KStatDeliveriesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KStatDeliveriesTable Test Case
 */
class KStatDeliveriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KStatDeliveriesTable
     */
    public $KStatDeliveries;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KStatDeliveries',
        'app.Organizations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KStatDeliveries') ? [] : ['className' => KStatDeliveriesTable::class];
        $this->KStatDeliveries = TableRegistry::getTableLocator()->get('KStatDeliveries', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KStatDeliveries);

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
