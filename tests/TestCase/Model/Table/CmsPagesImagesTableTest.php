<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CmsPagesImagesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CmsPagesImagesTable Test Case
 */
class CmsPagesImagesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CmsPagesImagesTable
     */
    public $CmsPagesImages;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CmsPagesImages',
        'app.Organizations',
        'app.CmsPages',
        'app.CmsImages',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CmsPagesImages') ? [] : ['className' => CmsPagesImagesTable::class];
        $this->CmsPagesImages = TableRegistry::getTableLocator()->get('CmsPagesImages', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CmsPagesImages);

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
