<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KProdGasPromotionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KProdGasPromotionsTable Test Case
 */
class KProdGasPromotionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KProdGasPromotionsTable
     */
    public $KProdGasPromotions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KProdGasPromotions',
        'app.Organizations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KProdGasPromotions') ? [] : ['className' => KProdGasPromotionsTable::class];
        $this->KProdGasPromotions = TableRegistry::getTableLocator()->get('KProdGasPromotions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KProdGasPromotions);

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
