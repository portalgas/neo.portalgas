<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KArticlesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KArticlesTable Test Case
 */
class KArticlesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KArticlesTable
     */
    public $KArticles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KArticles',
        'app.Organizations',
        'app.SupplierOrganizations',
        'app.CategoryArticles'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KArticles') ? [] : ['className' => KArticlesTable::class];
        $this->KArticles = TableRegistry::getTableLocator()->get('KArticles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KArticles);

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
