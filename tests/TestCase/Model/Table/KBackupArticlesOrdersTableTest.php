<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KBackupArticlesOrdersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KBackupArticlesOrdersTable Test Case
 */
class KBackupArticlesOrdersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KBackupArticlesOrdersTable
     */
    public $KBackupArticlesOrders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KBackupArticlesOrders',
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
        $config = TableRegistry::getTableLocator()->exists('KBackupArticlesOrders') ? [] : ['className' => KBackupArticlesOrdersTable::class];
        $this->KBackupArticlesOrders = TableRegistry::getTableLocator()->get('KBackupArticlesOrders', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KBackupArticlesOrders);

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
