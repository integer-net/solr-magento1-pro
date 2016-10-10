<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Solr
 * @copyright  Copyright (c) 2016 integer_net GmbH (http://www.integer-net.de/)
 * @author     Andreas von Studnitz <avs@integer-net.de>
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

$installer->addAttribute('catalog_category', 'filter_position', array(
    'type'              => 'int',
    'input'             => 'select',
    'source'            => 'integernet_solr/eav_source_filterPosition',
    'label'             => 'Position of Filters',
    'required'          => 0,
    'user_defined'      => 0,
    'group'             => 'Solr',
    'global'            => 0,
    'visible'           => 1,
));

if (!$installer->getConnection()->tableColumnExists($installer->getTable('cms/page'), 'solr_boost')) {
    $installer->getConnection()->addColumn($installer->getTable('cms/page'), 'solr_boost', 'float( 12,4 ) UNSIGNED NOT NULL DEFAULT 1');
    $installer->getConnection()->addColumn($installer->getTable('cms/page'), 'solr_exclude', 'smallint(6) UNSIGNED NOT NULL DEFAULT 0');
    if (Mage::helper('core')->isModuleEnabled('Enterprise_Cms') && $installer->getConnection()->isTableExists($installer->getTable('enterprise_cms/page_revision'))) {
        $installer->getConnection()->addColumn($installer->getTable('enterprise_cms/page_revision'), 'solr_boost', 'float( 12,4 ) UNSIGNED NOT NULL DEFAULT 1');
        $installer->getConnection()->addColumn($installer->getTable('enterprise_cms/page_revision'), 'solr_exclude', 'smallint(6) UNSIGNED NOT NULL DEFAULT 0');
    }
}

$installer->endSetup();