<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KTemplatesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KTemplatesTable Test Case
 */
class KTemplatesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KTemplatesTable
     */
    public $KTemplates;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KTemplates',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KTemplates') ? [] : ['className' => KTemplatesTable::class];
        $this->KTemplates = TableRegistry::getTableLocator()->get('KTemplates', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KTemplates);

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
