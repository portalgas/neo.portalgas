<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KStatArticlesOrdersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KStatArticlesOrdersTable Test Case
 */
class KStatArticlesOrdersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KStatArticlesOrdersTable
     */
    public $KStatArticlesOrders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KStatArticlesOrders',
        'app.Organizations',
        'app.StatOrders',
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
        $config = TableRegistry::getTableLocator()->exists('KStatArticlesOrders') ? [] : ['className' => KStatArticlesOrdersTable::class];
        $this->KStatArticlesOrders = TableRegistry::getTableLocator()->get('KStatArticlesOrders', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KStatArticlesOrders);

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
