<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\OfferDetailStatesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\OfferDetailStatesTable Test Case
 */
class OfferDetailStatesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\OfferDetailStatesTable
     */
    public $OfferDetailStates;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.OfferDetailStates',
        'app.OfferDetails'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('OfferDetailStates') ? [] : ['className' => OfferDetailStatesTable::class];
        $this->OfferDetailStates = TableRegistry::getTableLocator()->get('OfferDetailStates', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->OfferDetailStates);

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
