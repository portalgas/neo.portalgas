<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KTemplatesOrdersStatesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KTemplatesOrdersStatesTable Test Case
 */
class KTemplatesOrdersStatesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KTemplatesOrdersStatesTable
     */
    public $KTemplatesOrdersStates;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KTemplatesOrdersStates',
        'app.Templates',
        'app.Groups',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KTemplatesOrdersStates') ? [] : ['className' => KTemplatesOrdersStatesTable::class];
        $this->KTemplatesOrdersStates = TableRegistry::getTableLocator()->get('KTemplatesOrdersStates', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KTemplatesOrdersStates);

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
