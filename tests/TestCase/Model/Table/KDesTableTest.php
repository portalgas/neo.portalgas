<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KDesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KDesTable Test Case
 */
class KDesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KDesTable
     */
    public $KDes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KDes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KDes') ? [] : ['className' => KDesTable::class];
        $this->KDes = TableRegistry::getTableLocator()->get('KDes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KDes);

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
}
