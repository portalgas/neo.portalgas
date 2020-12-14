<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KStoreroomsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KStoreroomsTable Test Case
 */
class KStoreroomsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KStoreroomsTable
     */
    public $KStorerooms;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KStorerooms',
        'app.Organizations',
        'app.Deliveries',
        'app.Users',
        'app.Articles',
        'app.ArticleOrganizations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KStorerooms') ? [] : ['className' => KStoreroomsTable::class];
        $this->KStorerooms = TableRegistry::getTableLocator()->get('KStorerooms', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KStorerooms);

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
