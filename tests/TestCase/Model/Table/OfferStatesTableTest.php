<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\OfferStatesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\OfferStatesTable Test Case
 */
class OfferStatesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\OfferStatesTable
     */
    public $OfferStates;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.OfferStates',
        'app.Offers'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('OfferStates') ? [] : ['className' => OfferStatesTable::class];
        $this->OfferStates = TableRegistry::getTableLocator()->get('OfferStates', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->OfferStates);

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
