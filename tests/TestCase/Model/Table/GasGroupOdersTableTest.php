<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\GasGroupOdersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\GasGroupOdersTable Test Case
 */
class GasGroupOdersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\GasGroupOdersTable
     */
    public $GasGroupOders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.GasGroupOders',
        'app.Organizations',
        'app.GasGroups',
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
        $config = TableRegistry::getTableLocator()->exists('GasGroupOders') ? [] : ['className' => GasGroupOdersTable::class];
        $this->GasGroupOders = TableRegistry::getTableLocator()->get('GasGroupOders', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GasGroupOders);

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
