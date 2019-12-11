<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\QuotesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\QuotesTable Test Case
 */
class QuotesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\QuotesTable
     */
    public $Quotes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Quotes',
        'app.Offers',
        'app.Customers',
        'app.GeoRegions',
        'app.GeoProvinces',
        'app.GeoComunes',
        'app.QuoteStates',
        'app.QuoteCollaborators',
        'app.QuoteDetails',
        'app.QuoteVoices'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Quotes') ? [] : ['className' => QuotesTable::class];
        $this->Quotes = TableRegistry::getTableLocator()->get('Quotes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Quotes);

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
