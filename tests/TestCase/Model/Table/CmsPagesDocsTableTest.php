<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CmsPagesDocsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CmsPagesDocsTable Test Case
 */
class CmsPagesDocsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CmsPagesDocsTable
     */
    public $CmsPagesDocs;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CmsPagesDocs',
        'app.Organizations',
        'app.CmsPages',
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
        $config = TableRegistry::getTableLocator()->exists('CmsPagesDocs') ? [] : ['className' => CmsPagesDocsTable::class];
        $this->CmsPagesDocs = TableRegistry::getTableLocator()->get('CmsPagesDocs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CmsPagesDocs);

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
