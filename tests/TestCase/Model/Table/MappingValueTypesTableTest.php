<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MappingValueTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MappingValueTypesTable Test Case
 */
class MappingValueTypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MappingValueTypesTable
     */
    public $MappingValueTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MappingValueTypes',
        'app.Mappings',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MappingValueTypes') ? [] : ['className' => MappingValueTypesTable::class];
        $this->MappingValueTypes = TableRegistry::getTableLocator()->get('MappingValueTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MappingValueTypes);

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
