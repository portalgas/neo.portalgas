<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\OfferVersionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\OfferVersionsTable Test Case
 */
class OfferVersionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\OfferVersionsTable
     */
    public $OfferVersions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.OfferVersions',
        'app.Offers'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('OfferVersions') ? [] : ['className' => OfferVersionsTable::class];
        $this->OfferVersions = TableRegistry::getTableLocator()->get('OfferVersions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->OfferVersions);

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
