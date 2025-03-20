<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CmsMenusDocsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CmsMenusDocsTable Test Case
 */
class CmsMenusDocsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CmsMenusDocsTable
     */
    public $CmsMenusDocs;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CmsMenusDocs',
        'app.Organizations',
        'app.CmsMenus',
        'app.CmsDocs',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CmsMenusDocs') ? [] : ['className' => CmsMenusDocsTable::class];
        $this->CmsMenusDocs = TableRegistry::getTableLocator()->get('CmsMenusDocs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CmsMenusDocs);

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
