<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ArticlesOrdersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ArticlesOrdersTable Test Case
 */
class ArticlesOrdersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ArticlesOrdersTable
     */
    public $ArticlesOrders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ArticlesOrders',
        'app.Organizations',
        'app.Orders',
        'app.ArticleOrganizations',
        'app.Articles',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ArticlesOrders') ? [] : ['className' => ArticlesOrdersTable::class];
        $this->ArticlesOrders = TableRegistry::getTableLocator()->get('ArticlesOrders', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ArticlesOrders);

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
