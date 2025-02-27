<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SummaryPaymentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SummaryPaymentsTable Test Case
 */
class SummaryPaymentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SummaryPaymentsTable
     */
    public $SummaryPayments;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.SummaryPayments',
        'app.Organizations',
        'app.Users',
        'app.RequestPayments',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('SummaryPayments') ? [] : ['className' => SummaryPaymentsTable::class];
        $this->SummaryPayments = TableRegistry::getTableLocator()->get('SummaryPayments', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SummaryPayments);

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
