<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KSummaryOrderCostLessesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KSummaryOrderCostLessesTable Test Case
 */
class KSummaryOrderCostLessesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KSummaryOrderCostLessesTable
     */
    public $KSummaryOrderCostLesses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KSummaryOrderCostLesses',
        'app.Organizations',
        'app.Users',
        'app.Orders',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KSummaryOrderCostLesses') ? [] : ['className' => KSummaryOrderCostLessesTable::class];
        $this->KSummaryOrderCostLesses = TableRegistry::getTableLocator()->get('KSummaryOrderCostLesses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KSummaryOrderCostLesses);

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
