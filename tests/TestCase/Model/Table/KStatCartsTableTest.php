<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KStatCartsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KStatCartsTable Test Case
 */
class KStatCartsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KStatCartsTable
     */
    public $KStatCarts;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KStatCarts',
        'app.Organizations',
        'app.Users',
        'app.ArticleOrganizations',
        'app.Articles',
        'app.StatOrders',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KStatCarts') ? [] : ['className' => KStatCartsTable::class];
        $this->KStatCarts = TableRegistry::getTableLocator()->get('KStatCarts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KStatCarts);

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
