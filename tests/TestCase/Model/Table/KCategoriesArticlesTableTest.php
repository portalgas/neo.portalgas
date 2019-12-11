<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KCategoriesArticlesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KCategoriesArticlesTable Test Case
 */
class KCategoriesArticlesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KCategoriesArticlesTable
     */
    public $KCategoriesArticles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KCategoriesArticles',
        'app.Organizations'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KCategoriesArticles') ? [] : ['className' => KCategoriesArticlesTable::class];
        $this->KCategoriesArticles = TableRegistry::getTableLocator()->get('KCategoriesArticles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KCategoriesArticles);

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
