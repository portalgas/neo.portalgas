<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KCartsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KCartsTable Test Case
 */
class KCartsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KCartsTable
     */
    public $KCarts;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KCarts',
        'app.Organizations',
        'app.Users',
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
        $config = TableRegistry::getTableLocator()->exists('KCarts') ? [] : ['className' => KCartsTable::class];
        $this->KCarts = TableRegistry::getTableLocator()->get('KCarts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KCarts);

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
