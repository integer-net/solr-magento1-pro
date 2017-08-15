<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Solr
 * @copyright  Copyright (c) 2015 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */

/**
 * @loadFixture registry
 * @loadFixture config
 */
class IntegerNet_SolrPro_Test_Controller_Robots extends IntegerNet_Solr_Test_Controller_Abstract
{
    protected function setUp()
    {
        parent::setUp();
        Mage::getModel('integernet_solr/indexer')->reindexAll();
    }

    /**
     * @test
     * @loadFixture catalog
     */
    public function shouldHideSearchResultPageFromRobots()
    {
        $this->dispatch('catalogsearch/result/index', ['q' => 'wells']);
        $this->assertResponseBodyContains('<meta name="robots" content="NOINDEX,NOFOLLOW" />');
    }

    /**
     * @test
     * @loadFixture catalog
     * @loadFixture config_robots_alternative
     */
    public function shouldNotHideSearchResultPageFromRobots()
    {
        $this->dispatch('catalogsearch/result/index', ['q' => 'wells']);
        $this->assertResponseBodyContains('<meta name="robots" content="INDEX,FOLLOW" />');
    }

    /**
     * @test
     * @loadFixture catalog
     */
    public function shouldHideSearchResultPageWithFiltersFromRobots()
    {
        $this->dispatch('catalogsearch/result/index', ['q' => 'wells', 'cat' => 21]);
        $this->assertResponseBodyContains('<meta name="robots" content="NOINDEX,NOFOLLOW" />');
    }

    /**
     * @test
     * @loadFixture catalog
     * @loadFixture config_robots_alternative
     */
    public function shouldHideSearchResultPageWithFiltersFromRobotsOnAlternativeConfig()
    {
        $this->dispatch('catalogsearch/result/index', ['q' => 'wells', 'cat' => 21]);
        $this->assertResponseBodyContains('<meta name="robots" content="NOINDEX,NOFOLLOW" />');
    }
}