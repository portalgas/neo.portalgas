<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PayTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PayTypesTable Test Case
 */
class PayTypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PayTypesTable
     */
    public $PayTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.PayTypes',
        'app.QuoteDetailCalendars',
        'app.Offers'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('PayTypes') ? [] : ['className' => PayTypesTable::class];
        $this->PayTypes = TableRegistry::getTableLocator()->get('PayTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PayTypes);

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
}
