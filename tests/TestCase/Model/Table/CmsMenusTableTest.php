<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CmsMenusTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CmsMenusTable Test Case
 */
class CmsMenusTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CmsMenusTable
     */
    public $CmsMenus;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CmsMenus',
        'app.Organizations',
        'app.CmsMenuTypes',
        'app.CmsPages',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CmsMenus') ? [] : ['className' => CmsMenusTable::class];
        $this->CmsMenus = TableRegistry::getTableLocator()->get('CmsMenus', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CmsMenus);

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
