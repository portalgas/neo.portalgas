<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KTemplatesDesOrdersStatesOrdersActionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KTemplatesDesOrdersStatesOrdersActionsTable Test Case
 */
class KTemplatesDesOrdersStatesOrdersActionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KTemplatesDesOrdersStatesOrdersActionsTable
     */
    public $KTemplatesDesOrdersStatesOrdersActions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KTemplatesDesOrdersStatesOrdersActions',
        'app.Templates',
        'app.Groups',
        'app.DesOrderActions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KTemplatesDesOrdersStatesOrdersActions') ? [] : ['className' => KTemplatesDesOrdersStatesOrdersActionsTable::class];
        $this->KTemplatesDesOrdersStatesOrdersActions = TableRegistry::getTableLocator()->get('KTemplatesDesOrdersStatesOrdersActions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KTemplatesDesOrdersStatesOrdersActions);

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
