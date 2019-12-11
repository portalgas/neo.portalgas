<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\OffersVoicesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\OffersVoicesTable Test Case
 */
class OffersVoicesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\OffersVoicesTable
     */
    public $OffersVoices;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.OffersVoices',
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
        $config = TableRegistry::getTableLocator()->exists('OffersVoices') ? [] : ['className' => OffersVoicesTable::class];
        $this->OffersVoices = TableRegistry::getTableLocator()->get('OffersVoices', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->OffersVoices);

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
