<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KCashesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KCashesTable Test Case
 */
class KCashesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KCashesTable
     */
    public $KCashes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KCashes',
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
        $config = TableRegistry::getTableLocator()->exists('KCashes') ? [] : ['className' => KCashesTable::class];
        $this->KCashes = TableRegistry::getTableLocator()->get('KCashes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KCashes);

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
