<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\JUserUsergroupMapTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\JUserUsergroupMapTable Test Case
 */
class JUserUsergroupMapTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\JUserUsergroupMapTable
     */
    public $JUserUsergroupMap;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.JUserUsergroupMap',
        'app.Users',
        'app.Groups'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('JUserUsergroupMap') ? [] : ['className' => JUserUsergroupMapTable::class];
        $this->JUserUsergroupMap = TableRegistry::getTableLocator()->get('JUserUsergroupMap', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->JUserUsergroupMap);

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
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
