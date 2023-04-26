<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KBookmarksMailsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KBookmarksMailsTable Test Case
 */
class KBookmarksMailsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KBookmarksMailsTable
     */
    public $KBookmarksMails;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KBookmarksMails',
        'app.Organizations',
        'app.Users',
        'app.SupplierOrganizations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KBookmarksMails') ? [] : ['className' => KBookmarksMailsTable::class];
        $this->KBookmarksMails = TableRegistry::getTableLocator()->get('KBookmarksMails', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KBookmarksMails);

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
