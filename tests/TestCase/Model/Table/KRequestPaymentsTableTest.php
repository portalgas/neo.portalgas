<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KRequestPaymentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KRequestPaymentsTable Test Case
 */
class KRequestPaymentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KRequestPaymentsTable
     */
    public $KRequestPayments;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KRequestPayments',
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
        $config = TableRegistry::getTableLocator()->exists('KRequestPayments') ? [] : ['className' => KRequestPaymentsTable::class];
        $this->KRequestPayments = TableRegistry::getTableLocator()->get('KRequestPayments', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KRequestPayments);

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
