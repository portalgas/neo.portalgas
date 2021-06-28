<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\JContentTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\JContentTable Test Case
 */
class JContentTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\JContentTable
     */
    public $JContent;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.JContent',
        'app.Assets',
        'app.KSuppliers',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('JContent') ? [] : ['className' => JContentTable::class];
        $this->JContent = TableRegistry::getTableLocator()->get('JContent', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->JContent);

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
