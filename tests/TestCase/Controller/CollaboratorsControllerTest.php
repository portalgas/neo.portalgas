<?php
namespace App\Test\TestCase\Controller;

use App\Controller\CollaboratorsController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\CollaboratorsController Test Case
 */
class CollaboratorsControllerTest extends TestCase
{
    use IntegrationTestTrait;

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
        'app.OfferCollaborators',
        'app.OfferDetailCollaborators',
        'app.Offers',
        'app.QuoteDetailCalendars',
        'app.QuoteDetailCollaborators'
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
