<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PriceTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PriceTypesTable Test Case
 */
class PriceTypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PriceTypesTable
     */
    public $PriceTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.PriceTypes',
        'app.Organizations',
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
        $config = TableRegistry::getTableLocator()->exists('PriceTypes') ? [] : ['className' => PriceTypesTable::class];
        $this->PriceTypes = TableRegistry::getTableLocator()->get('PriceTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PriceTypes);

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
