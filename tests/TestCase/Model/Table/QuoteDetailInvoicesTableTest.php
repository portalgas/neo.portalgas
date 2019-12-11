<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\QuoteDetailInvoicesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\QuoteDetailInvoicesTable Test Case
 */
class QuoteDetailInvoicesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\QuoteDetailInvoicesTable
     */
    public $QuoteDetailInvoices;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.QuoteDetailInvoices',
        'app.QuoteDetails',
        'app.PayTypes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('QuoteDetailInvoices') ? [] : ['className' => QuoteDetailInvoicesTable::class];
        $this->QuoteDetailInvoices = TableRegistry::getTableLocator()->get('QuoteDetailInvoices', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->QuoteDetailInvoices);

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
