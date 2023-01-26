<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KOrdersActionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KOrdersActionsTable Test Case
 */
class KOrdersActionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KOrdersActionsTable
     */
    public $KOrdersActions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KOrdersActions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KOrdersActions') ? [] : ['className' => KOrdersActionsTable::class];
        $this->KOrdersActions = TableRegistry::getTableLocator()->get('KOrdersActions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KOrdersActions);

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
