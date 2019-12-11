<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\OfferCollaboratorsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\OfferCollaboratorsTable Test Case
 */
class OfferCollaboratorsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\OfferCollaboratorsTable
     */
    public $OfferCollaborators;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.OfferCollaborators',
        'app.Offers',
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
        $config = TableRegistry::getTableLocator()->exists('OfferCollaborators') ? [] : ['className' => OfferCollaboratorsTable::class];
        $this->OfferCollaborators = TableRegistry::getTableLocator()->get('OfferCollaborators', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->OfferCollaborators);

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
