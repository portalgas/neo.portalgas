<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KArticlesOrdersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KArticlesOrdersTable Test Case
 */
class KArticlesOrdersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KArticlesOrdersTable
     */
    public $KArticlesOrders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KArticlesOrders',
        'app.Organizations',
        'app.Orders',
        'app.ArticleOrganizations',
        'app.Articles'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KArticlesOrders') ? [] : ['className' => KArticlesOrdersTable::class];
        $this->KArticlesOrders = TableRegistry::getTableLocator()->get('KArticlesOrders', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KArticlesOrders);

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
