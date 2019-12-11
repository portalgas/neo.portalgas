<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CollaboratorsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CollaboratorsTable Test Case
 */
class CollaboratorsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CollaboratorsTable
     */
    public $Collaborators;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Collaborators',
        'app.Users',
        'app.GeoRegions',
        'app.GeoProvinces',
        'app.GeoComunes',
        'app.CollaboratorCollaboratorActivities',
        'app.CollaboratorCollaboratorTypes',
        'app.Customers',
        'app.OfferDetailCollaborators',
        'app.Offers',
        'app.QuoteCollaborators',
        'app.QuoteDetailCalendars'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Collaborators') ? [] : ['className' => CollaboratorsTable::class];
        $this->Collaborators = TableRegistry::getTableLocator()->get('Collaborators', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Collaborators);

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
