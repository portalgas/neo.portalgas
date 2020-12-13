<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KSummaryOrderTrasportsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KSummaryOrderTrasportsTable Test Case
 */
class KSummaryOrderTrasportsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KSummaryOrderTrasportsTable
     */
    public $KSummaryOrderTrasports;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KSummaryOrderTrasports',
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
        $config = TableRegistry::getTableLocator()->exists('KSummaryOrderTrasports') ? [] : ['className' => KSummaryOrderTrasportsTable::class];
        $this->KSummaryOrderTrasports = TableRegistry::getTableLocator()->get('KSummaryOrderTrasports', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KSummaryOrderTrasports);

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
