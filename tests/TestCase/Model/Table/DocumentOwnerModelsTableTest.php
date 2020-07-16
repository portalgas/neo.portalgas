<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DocumentOwnerModelsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DocumentOwnerModelsTable Test Case
 */
class DocumentOwnerModelsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\DocumentOwnerModelsTable
     */
    public $DocumentOwnerModels;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.DocumentOwnerModels',
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
        $config = TableRegistry::getTableLocator()->exists('DocumentOwnerModels') ? [] : ['className' => DocumentOwnerModelsTable::class];
        $this->DocumentOwnerModels = TableRegistry::getTableLocator()->get('DocumentOwnerModels', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DocumentOwnerModels);

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
