<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_SolrPro
 * @copyright  Copyright (c) 2016 integer_net GmbH (http://www.integer-net.de/)
 * @author     Andreas von Studnitz <avs@integer-net.de>
 */
class IntegerNet_SolrPro_Model_Observer
{
    /**
     * Rebuilt Solr Cache on config save
     * Check if cronjobs are active
     *
     * @param Varien_Event_Observer $observer
     */
    public function adminSystemConfigChangedSectionIntegernetSolr(Varien_Event_Observer $observer)
    {
        Mage::helper('integernet_solrpro')->autosuggest()->storeSolrConfig();

        if (!Mage::getStoreConfigFlag('integernet_solr/connection_check/is_active')) {
            return;
        }
        $cronCollection = Mage::getResourceModel('cron/schedule_collection')
            ->addFieldToFilter('created_at', array('gt' => Zend_Date::now()->subDay(2)->get(Zend_Date::ISO_8601)));
        if (!$cronCollection->getSize()) {
            Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('integernet_solr')->__(
                'It seems you have no cronjobs running. They are needed for doing regular connection checks. We strongly suggest you setup cronjobs. See <a href="http://www.magentocommerce.com/wiki/1_-_installation_and_configuration/how_to_setup_a_cron_job" target="_blank">here</a> for details.'
            ));
        }
    }

    public function controllerActionPredispatchCatalogCategoryView(Varien_Event_Observer $observer)
    {
        if (Mage::getStoreConfigFlag('integernet_solr/general/is_active')
            && Mage::getStoreConfigFlag('integernet_solr/category/is_active')) {
            if (!class_exists('\IntegerNet\SolrSuggest\Util\Version')) {
                Mage::throwException(
                    Mage::helper('integernet_solr')->__(
                        'The IntegerNet_Solr Pro library is not installed. You can get it from <a href="%s" target="_blank">%s</a>.',
                        'https://github.com/integer-net/solr-pro',
                        'https://github.com/integer-net/solr-pro'
                    )
                );
            }

            if (!$this->_getPingResult()) {
                Mage::app()->getStore()->setConfig('integernet_solr/general/is_active', 0);
            }
        }

        if (!Mage::getStoreConfigFlag('integernet_solr/general/is_active')) {
            Mage::app()->getStore()->setConfig('integernet_solr/category/is_active', 0);
        }

        /** @var Mage_Core_Controller_Varien_Action $action */
        $action = $observer->getControllerAction();

        if (Mage::helper('integernet_solr')->module()->isActive() && $order = $action->getRequest()->getParam('order')) {
            if ($order === 'relevance') {
                $_GET['order'] = 'position';
            }
        }
    }

    /**
     * Regenerate config if all cache should be deleted.
     *
     * @param Varien_Event_Observer $observer
     */
    public function applicationCleanCache(Varien_Event_Observer $observer)
    {
        $tags = $observer->getTags();
        if (!is_array($tags) || sizeof($tags)) {
            return;
        }
        Mage::helper('integernet_solrpro')->autosuggest()->storeSolrConfig();
    }

    /**
     * Store Solr configuration in serialized text field so it can be accessed from autosuggest later
     */
    public function storeSolrConfig()
    {
        Mage::helper('integernet_solrpro')->autosuggest()->storeSolrConfig();
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function adminSessionUserLoginSuccess($observer)
    {
        if (!Mage::getStoreConfigFlag('integernet_solr/general/is_active')) {
            return;
        }

        if (!trim(Mage::getStoreConfig('integernet_solr/general/license_key'))) {

            if ($installTimestamp = Mage::getStoreConfig('integernet_solr/general/install_date')) {

                $diff = time() - $installTimestamp;
                if (($diff < 0) || ($diff > 2419200)) {

                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('integernet_solrpro')->__('You haven\'t entered your license key for the IntegerNet_Solr module yet. The module has been disabled automatically.')
                    );

                } else {

                    Mage::getSingleton('adminhtml/session')->addWarning(
                        Mage::helper('integernet_solrpro')->__('You haven\'t entered your license key for the IntegerNet_Solr module yet. The module will stop working four weeks after installation.')
                    );
                }
            }

        }
    }

    public function checkSolrServerConnection()
    {
        Mage::getSingleton('integernet_solrpro/connectionCheck')->checkConnection();
    }

    /**
     * Add new fields to CMS Page edit form
     *
     * @param Varien_Event_Observer $observer
     */
    public function adminhtmlCmsPageEditTabContentPrepareForm(Varien_Event_Observer $observer)
    {
        $model = Mage::registry('cms_page');
        $form = $observer->getForm();
        $fieldset = $form->addFieldset('integernet_solr_fieldset', array('legend'=>Mage::helper('integernet_solr')->__('Solr'),'class'=>'fieldset-wide'));
        $fieldset->addField('solr_exclude', 'select', array(
            'name'      => 'solr_exclude',
            'label'     => Mage::helper('integernet_solr')->__('Exclude this Page from Solr Index'),
            'title'     => Mage::helper('integernet_solr')->__('Exclude this Page from Solr Index'),
            'disabled'  => false,
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'value'     => $model->getData('solr_exclude')
        ));

        $field = $fieldset->addField('solr_boost', 'text', array(
            'name' => 'solr_boost',
            'label' => Mage::helper('integernet_solr')->__('Solr Priority'),
            'title' => Mage::helper('integernet_solr')->__('Solr Priority'),
            'note' => Mage::helper('integernet_solr')->__('1 is default, use higher numbers for higher priority.'),
            'class' => 'validate-number',
            'value'     => $model->getData('solr_boost')
        ));

        // Set default value
        if (!$model->getId()) {
            $field->setValue('1.0000');
        }

    }

    /**
     * Clean full category image cache in response to catalog (product) image cache clean
     *
     * @param $observer
     */
    public function cleanImageCache(Varien_Event_Observer $observer)
    {
        $cacheDir = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'cache';
        mageDelTree($cacheDir);
    }

    /**
     * @return bool
     */
    protected function _getPingResult()
    {
        $solr = Mage::helper('integernet_solr')->factory()->getSolrResource()->getSolrService(Mage::app()->getStore()->getId());
        return (boolean)$solr->ping();
    }

    /**
     * Adjust robots tag of page head depending on current page and configuration setting
     *
     * @param Varien_Event_Observer $observer
     */
    public function coreBlockAbstractToHtmlBefore(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();

        if ($block instanceof Mage_Page_Block_Html_Head) {
            $this->_adjustRobots($block);
        }
    }

    /**
     * Set Robots to NOINDEX,NOFOLLOW depending on config
     *
     * @param Mage_Page_Block_Html_Head $block
     */
    protected function _adjustRobots($block)
    {
        /** @var $helper IntegerNet_Solr_Helper_Data */
        $helper = Mage::helper('integernet_solr');
        if (!$helper->module()->isActive()) {
            return;
        }
        $stateBlock = null;
        $robotOptions = explode(',', Mage::getStoreConfig('integernet_solr/seo/hide_from_robots'));
        if ($helper->page()->isSearchPage()) {
            if (in_array('search_results_all', $robotOptions)) {
                $block->setData('robots', 'NOINDEX,NOFOLLOW');
                return;
            }
            if (!in_array('search_results_filtered', $robotOptions)) {
                return;
            }
            /** @var IntegerNet_Solr_Block_Result_Layer_State $stateBlock */
            $stateBlock = $block->getLayout()->getBlock('catalogsearch.solr.layer.state');
        } elseif ($helper->page()->isCategoryPage() && $helper->isCategoryDisplayActive()) {
            if (!in_array('categories_filtered', $robotOptions)) {
                return;
            }
            /** @var IntegerNet_Solr_Block_Result_Layer_State $stateBlock */
            $stateBlock = $block->getLayout()->getBlock('catalog.solr.layer.state');
        }
        if ($stateBlock instanceof IntegerNet_Solr_Block_Result_Layer_State) {
            $activeFilters = $stateBlock->getActiveFilters();
            if (is_array($activeFilters) && sizeof($activeFilters)) {
                $block->setData('robots', 'NOINDEX,NOFOLLOW');
            }
        }
    }
}