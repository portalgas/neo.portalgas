<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KTemplatesOrdersStatesOrdersActionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KTemplatesOrdersStatesOrdersActionsTable Test Case
 */
class KTemplatesOrdersStatesOrdersActionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KTemplatesOrdersStatesOrdersActionsTable
     */
    public $KTemplatesOrdersStatesOrdersActions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KTemplatesOrdersStatesOrdersActions',
        'app.Templates',
        'app.Groups',
        'app.OrderActions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KTemplatesOrdersStatesOrdersActions') ? [] : ['className' => KTemplatesOrdersStatesOrdersActionsTable::class];
        $this->KTemplatesOrdersStatesOrdersActions = TableRegistry::getTableLocator()->get('KTemplatesOrdersStatesOrdersActions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KTemplatesOrdersStatesOrdersActions);

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
