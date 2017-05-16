IntegerNet_Solr Pro for Magento 1
===============
Release Notes

Version 1.7.4 (May 16, 2017)
---------------

- Add slicing functionality (see src/shell/integernet-solr.php)
- Fix bug with indexing multistore products with activated flat catalog
- Exclude items with zero counts from filter items count calculation (thanks @tim-bezhashvyly!)
- Fallback to Magento if category is excluded from Solr index (thanks @tim-bezhashvyly!)
- Move files regarding autosuggest to Pro module 

Version 1.7.3 (May 3, 2017)
---------------

- Support Indexing of Bundle Child Products
- Optimization of Search Results for search queries containing numbers
- Bugfix regarding Flat Product Setting
- Bugfix regarding Flat Category Setting
- Bugfix regarding Re-Initialization of MySQL Connection
- Bugfix regarding duplicate Layout Update (thanks @tim-bezhashvyly!)

Version 1.7.2 (Dec 19, 2016)
---------------

- Change Request mode from GET to POST
- Fix bug with different default sort order in categories

Version 1.7.0 (Oct 12, 2016)
---------------

- First public release of IntegerNet_Solr