<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CmsMenuTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CmsMenuTypesTable Test Case
 */
class CmsMenuTypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CmsMenuTypesTable
     */
    public $CmsMenuTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CmsMenuTypes',
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
        $config = TableRegistry::getTableLocator()->exists('CmsMenuTypes') ? [] : ['className' => CmsMenuTypesTable::class];
        $this->CmsMenuTypes = TableRegistry::getTableLocator()->get('CmsMenuTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CmsMenuTypes);

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
