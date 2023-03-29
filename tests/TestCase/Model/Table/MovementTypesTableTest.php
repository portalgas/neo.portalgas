<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MovementTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MovementTypesTable Test Case
 */
class MovementTypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MovementTypesTable
     */
    public $MovementTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MovementTypes',
        'app.Movements',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MovementTypes') ? [] : ['className' => MovementTypesTable::class];
        $this->MovementTypes = TableRegistry::getTableLocator()->get('MovementTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MovementTypes);

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
