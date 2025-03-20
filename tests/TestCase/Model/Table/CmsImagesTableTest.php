<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CmsImagesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CmsImagesTable Test Case
 */
class CmsImagesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CmsImagesTable
     */
    public $CmsImages;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CmsImages',
        'app.Organizations',
        'app.CmsPagesImages',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CmsImages') ? [] : ['className' => CmsImagesTable::class];
        $this->CmsImages = TableRegistry::getTableLocator()->get('CmsImages', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CmsImages);

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
