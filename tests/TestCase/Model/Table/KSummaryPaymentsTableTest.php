<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SummaryPaymentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KSummaryPaymentsTable Test Case
 */
class KSummaryPaymentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SummaryPaymentsTable
     */
    public $KSummaryPayments;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KSummaryPayments',
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
        $config = TableRegistry::getTableLocator()->exists('KSummaryPayments') ? [] : ['className' => SummaryPaymentsTable::class];
        $this->KSummaryPayments = TableRegistry::getTableLocator()->get('KSummaryPayments', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KSummaryPayments);

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
