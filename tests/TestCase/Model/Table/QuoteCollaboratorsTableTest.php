<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\QuoteCollaboratorsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\QuoteCollaboratorsTable Test Case
 */
class QuoteCollaboratorsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\QuoteCollaboratorsTable
     */
    public $QuoteCollaborators;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.QuoteCollaborators',
        'app.Quotes',
        'app.Collaborators',
        'app.PriceTypes',
        'app.PayTypes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('QuoteCollaborators') ? [] : ['className' => QuoteCollaboratorsTable::class];
        $this->QuoteCollaborators = TableRegistry::getTableLocator()->get('QuoteCollaborators', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->QuoteCollaborators);

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
