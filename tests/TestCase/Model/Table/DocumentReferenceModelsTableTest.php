<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DocumentReferenceModelsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DocumentReferenceModelsTable Test Case
 */
class DocumentReferenceModelsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\DocumentReferenceModelsTable
     */
    public $DocumentReferenceModels;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.DocumentReferenceModels',
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
        $config = TableRegistry::getTableLocator()->exists('DocumentReferenceModels') ? [] : ['className' => DocumentReferenceModelsTable::class];
        $this->DocumentReferenceModels = TableRegistry::getTableLocator()->get('DocumentReferenceModels', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DocumentReferenceModels);

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
