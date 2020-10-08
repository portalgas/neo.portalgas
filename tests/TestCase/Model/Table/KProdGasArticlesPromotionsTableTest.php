<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KProdGasArticlesPromotionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KProdGasArticlesPromotionsTable Test Case
 */
class KProdGasArticlesPromotionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KProdGasArticlesPromotionsTable
     */
    public $KProdGasArticlesPromotions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KProdGasArticlesPromotions',
        'app.Organizations',
        'app.ProdGasPromotions',
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
        $config = TableRegistry::getTableLocator()->exists('KProdGasArticlesPromotions') ? [] : ['className' => KProdGasArticlesPromotionsTable::class];
        $this->KProdGasArticlesPromotions = TableRegistry::getTableLocator()->get('KProdGasArticlesPromotions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KProdGasArticlesPromotions);

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
