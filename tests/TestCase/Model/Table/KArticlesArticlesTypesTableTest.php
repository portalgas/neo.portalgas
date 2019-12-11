<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KArticlesArticlesTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KArticlesArticlesTypesTable Test Case
 */
class KArticlesArticlesTypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KArticlesArticlesTypesTable
     */
    public $KArticlesArticlesTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KArticlesArticlesTypes',
        'app.Organizations',
        'app.Articles',
        'app.ArticleTypes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KArticlesArticlesTypes') ? [] : ['className' => KArticlesArticlesTypesTable::class];
        $this->KArticlesArticlesTypes = TableRegistry::getTableLocator()->get('KArticlesArticlesTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KArticlesArticlesTypes);

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
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
