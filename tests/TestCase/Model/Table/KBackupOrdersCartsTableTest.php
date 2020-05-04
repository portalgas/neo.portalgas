<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KBackupOrdersCartsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KBackupOrdersCartsTable Test Case
 */
class KBackupOrdersCartsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KBackupOrdersCartsTable
     */
    public $KBackupOrdersCarts;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KBackupOrdersCarts',
        'app.Organizations',
        'app.Users',
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
        $config = TableRegistry::getTableLocator()->exists('KBackupOrdersCarts') ? [] : ['className' => KBackupOrdersCartsTable::class];
        $this->KBackupOrdersCarts = TableRegistry::getTableLocator()->get('KBackupOrdersCarts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KBackupOrdersCarts);

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
