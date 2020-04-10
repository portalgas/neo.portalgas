<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KCashesHistoriesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KCashesHistoriesTable Test Case
 */
class KCashesHistoriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KCashesHistoriesTable
     */
    public $KCashesHistories;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KCashesHistories',
        'app.Organizations',
        'app.Cashes',
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
        $config = TableRegistry::getTableLocator()->exists('KCashesHistories') ? [] : ['className' => KCashesHistoriesTable::class];
        $this->KCashesHistories = TableRegistry::getTableLocator()->get('KCashesHistories', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KCashesHistories);

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
