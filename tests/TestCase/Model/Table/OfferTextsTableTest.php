<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\OfferTextsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\OfferTextsTable Test Case
 */
class OfferTextsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\OfferTextsTable
     */
    public $OfferTexts;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.OfferTexts',
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
        $config = TableRegistry::getTableLocator()->exists('OfferTexts') ? [] : ['className' => OfferTextsTable::class];
        $this->OfferTexts = TableRegistry::getTableLocator()->get('OfferTexts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->OfferTexts);

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
