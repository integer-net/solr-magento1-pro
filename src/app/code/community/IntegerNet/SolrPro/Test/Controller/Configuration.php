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
class IntegerNet_SolrPro_Test_Controller_Configuration extends IntegerNet_Solr_Test_Controller_Abstract
{
    /**
     * @test
     */
    public function shouldShowStatusBlockWithLicenseMessage()
    {
        $this->adminSession();
        $this->dispatch('adminhtml/system_config/edit', ['section' => 'integernet_solr']);
        $this->assertRequestRoute('adminhtml/system_config/edit');
        $this->assertLayoutBlockRendered('integernet_solr_config_status');

        $this->assertResponseBodyContains('You haven\'t entered your license key yet.');
    }

}