<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\QuoteDetailCollaboratorsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\QuoteDetailCollaboratorsTable Test Case
 */
class QuoteDetailCollaboratorsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\QuoteDetailCollaboratorsTable
     */
    public $QuoteDetailCollaborators;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.QuoteDetailCollaborators',
        'app.QuoteDetails',
        'app.Collaborators',
        'app.PriceTypes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('QuoteDetailCollaborators') ? [] : ['className' => QuoteDetailCollaboratorsTable::class];
        $this->QuoteDetailCollaborators = TableRegistry::getTableLocator()->get('QuoteDetailCollaborators', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->QuoteDetailCollaborators);

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
