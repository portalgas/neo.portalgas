<?php
namespace App\Test\TestCase\Controller;

use App\Controller\KBackupOrdersOrdersController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\KBackupOrdersOrdersController Test Case
 *
 * @uses \App\Controller\KBackupOrdersOrdersController
 */
class KBackupOrdersOrdersControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KBackupOrdersOrders',
        'app.Organizations',
        'app.SupplierOrganizations',
        'app.OwnerOrganizations',
        'app.OwnerSupplierOrganizations',
        'app.Deliveries',
        'app.ProdGasPromotions',
        'app.DesOrders',
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
