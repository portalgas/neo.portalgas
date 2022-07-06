<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SocialmarketCartsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SocialmarketCartsTable Test Case
 */
class SocialmarketCartsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SocialmarketCartsTable
     */
    public $SocialmarketCarts;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.SocialmarketCarts',
        'app.Organizations',
        'app.Users',
        'app.UserOrganizations',
        'app.Orders',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('SocialmarketCarts') ? [] : ['className' => SocialmarketCartsTable::class];
        $this->SocialmarketCarts = TableRegistry::getTableLocator()->get('SocialmarketCarts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SocialmarketCarts);

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
