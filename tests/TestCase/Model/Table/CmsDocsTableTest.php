<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CmsDocsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CmsDocsTable Test Case
 */
class CmsDocsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CmsDocsTable
     */
    public $CmsDocs;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CmsDocs',
        'app.Organizations',
        'app.CmsMenus',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CmsDocs') ? [] : ['className' => CmsDocsTable::class];
        $this->CmsDocs = TableRegistry::getTableLocator()->get('CmsDocs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CmsDocs);

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
