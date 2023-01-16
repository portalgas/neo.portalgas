<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KDesSuppliersReferentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KDesSuppliersReferentsTable Test Case
 */
class KDesSuppliersReferentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KDesSuppliersReferentsTable
     */
    public $KDesSuppliersReferents;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KDesSuppliersReferents',
        'app.Des',
        'app.DesSuppliers',
        'app.Organizations',
        'app.Users',
        'app.Groups',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KDesSuppliersReferents') ? [] : ['className' => KDesSuppliersReferentsTable::class];
        $this->KDesSuppliersReferents = TableRegistry::getTableLocator()->get('KDesSuppliersReferents', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KDesSuppliersReferents);

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
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
