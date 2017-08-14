IntegerNet_Solr Pro for Magento 1
===============
Release Notes

Version 1.7.8 (Aug 14, 2017)
---------------

- Make the MySQL connection disconnect/reconnect feature configurable (default: off) 

Version 1.7.7 (Jul 17, 2017)
---------------

- Choose if you want to display products which are not in stock via a new configuration setting 
(separately for search results, category pages and autosuggest)
- Configure the score multiplier for products which are not in stock 
- Correctly disable category and CMS result blocks if module is disabled (thanks Thomas Zöger!)

Version 1.7.6 (May 18, 2017)
---------------

- Bugfix regarding RWD theme

Version 1.7.5 (May 16, 2017)
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

Version 1.6.0 (Aug 1, 2016)
---------------

- Improved search algorithm, e.g. for search terms containing both words and numbers
- Category and CMS pages in autosuggest results
- More configuration options for filters
- Improved compatibility with other third party extensions

Version 1.5.0 (Mar 7, 2016)
---------------

This is a completely refactored version. Plus, we added quite a number of new features, improving usability, search results and SEO:    

- Select multiple filter values
- Horizontal display of filters on search result pages
- Select per category which filters shall be available
- Configurable meta robots tag (NOINDEX, NOFOLLOW) for search result pages, filtered search results and filtered category pages
- Adjustable boost for products in search results if they are in a category whose name matches the search term
- Direct redirects to a product or category page if the search term is an exact match with the product name, SKU, category name, or a different attribute of your choice
