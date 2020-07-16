<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DocumentStatesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DocumentStatesTable Test Case
 */
class DocumentStatesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\DocumentStatesTable
     */
    public $DocumentStates;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.DocumentStates',
        'app.Documents',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('DocumentStates') ? [] : ['className' => DocumentStatesTable::class];
        $this->DocumentStates = TableRegistry::getTableLocator()->get('DocumentStates', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DocumentStates);

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
