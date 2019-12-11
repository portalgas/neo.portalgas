<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KArticlesTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KArticlesTypesTable Test Case
 */
class KArticlesTypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KArticlesTypesTable
     */
    public $KArticlesTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KArticlesTypes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KArticlesTypes') ? [] : ['className' => KArticlesTypesTable::class];
        $this->KArticlesTypes = TableRegistry::getTableLocator()->get('KArticlesTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KArticlesTypes);

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
