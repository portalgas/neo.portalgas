<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KSuppliersVotesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KSuppliersVotesTable Test Case
 */
class KSuppliersVotesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KSuppliersVotesTable
     */
    public $KSuppliersVotes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KSuppliersVotes',
        'app.Suppliers',
        'app.Organizations',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KSuppliersVotes') ? [] : ['className' => KSuppliersVotesTable::class];
        $this->KSuppliersVotes = TableRegistry::getTableLocator()->get('KSuppliersVotes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KSuppliersVotes);

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
