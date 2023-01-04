<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KLoopsDeliveriesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KLoopsDeliveriesTable Test Case
 */
class KLoopsDeliveriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KLoopsDeliveriesTable
     */
    public $KLoopsDeliveries;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KLoopsDeliveries',
        'app.Organizations',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KLoopsDeliveries') ? [] : ['className' => KLoopsDeliveriesTable::class];
        $this->KLoopsDeliveries = TableRegistry::getTableLocator()->get('KLoopsDeliveries', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KLoopsDeliveries);

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
