<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CmsPageImagesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CmsPageImagesTable Test Case
 */
class CmsPageImagesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CmsPageImagesTable
     */
    public $CmsPageImages;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CmsPageImages',
        'app.CmsPages',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CmsPageImages') ? [] : ['className' => CmsPageImagesTable::class];
        $this->CmsPageImages = TableRegistry::getTableLocator()->get('CmsPageImages', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CmsPageImages);

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
