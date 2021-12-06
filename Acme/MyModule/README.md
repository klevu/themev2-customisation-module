Klevu ThemeV2 Customisation Examples
====================================

This demonstration module provides a "jumping off point" for creating your own
custom extension for JSv2 customisations.  
You can install this package out of the box to see examples of the following functionality

* Adding custom styling to the quick search results element
* Overriding the API call for quick search queries to modify the number of suggestions returned
* Modifying the minimum number of characters a visitor must enter before search suggestions are shown
* Implementing a custom template for quick search suggestions (to prepend a string to the product name)
* Implementing a custom template for the Klevu-powered search results page (to prepend a string to the results returned header)

Adding Custom Styling to the Quick Search Results
-------------------------------------------------

The following files are involved in this customisation

* [view/frontend/web/css/source/_module.less](view/frontend/web/css/source/_module.less)

Here we simply add a stylesheet to our module containing style changes to headers and borders in the quick
search results box. We have used Magento's preferred LESS format, which is [compiled during deployment](https://devdocs.magento.com/guides/v2.4/frontend-dev-guide/css-topics/css-preprocess.html)
and does not require layout files for inclusion.

Alternatively, you could create a plain CSS file and include it on relevant pages, as per 
[this guide in the Magento documentation](https://devdocs.magento.com/guides/v2.4/frontend-dev-guide/css-topics/css-themes.html).

To disable this customisation, simple remove or rename the [`view/frontend/web/css/source/_module.less`](view/frontend/web/css/source/_module.less) file.

Overriding the Quick Search API Call
------------------------------------

The following files are involved in this customisation

* [etc/di.xml](etc/di.xml)
* [Service/InteractiveOptionsProvider.php](Service/InteractiveOptionsProvider.php)
* [view/frontend/layout/default_head_blocks.xml](view/frontend/layout/default_head_blocks.xml)
* [view/frontend/templates/html/head/js_init/additional_before/quicksearch.phtml](view/frontend/templates/html/head/js_init/additional_before/quicksearch.phtml)

This customisation will change the maximum number of suggestions for each quick search section (CMS, categories, products)
from the default 3 to 15.

To implement, we initially create the [InteractiveOptionsProvider](Service/InteractiveOptionsProvider.php) to defer
powering up the quick search functionality.  
The relevant return value is
```php
return [
    'powerUp' => [
        // Defer quick search power up
        'quick' => false,
    ],
];
```
In this class we also inject and query an instance of `Klevu\FrontendJs\Api\IsEnabledConditionInterface` to
ensure we only provide our customisations to the core Klevu modules if Search is enabled on your store and you
are using ThemeV2.

This provider is registered in [`etc/di.xml`](etc/di.xml). Here we also inject the `IsEnabledCondition` from  the 
`Klevu_Search` module so our changes are output under the same conditions as the core settings.  
You can create your own IsEnabledCondition to use here by implementing the `Klevu\FrontendJs\Api\IsEnabledConditionInterface`
class, but that is out of scope for these examples.

In addition to providing power up configuration, we add some JavaScript to the `klevu.interactive` call by
creating the template [`view/frontend/templates/html/head/js_init/additional_before/quicksearch.phtml`](view/frontend/templates/html/head/js_init/additional_before/quicksearch.phtml)
and inserting into each page via the layout instructions in [`view/frontend/layout/default_head_blocks.xml`](view/frontend/layout/default_head_blocks.xml).

Within this file, the `klevu.coreEvent.build({` section is shared with the template override below, while the
`klevu.coreEvent.attach("setRemoteConfigQuickOverride", {` section contains specific code for both this change
(overriding the API call) and the template change.

To disable this customisation, comment out the following section in `view/frontend/templates/html/head/js_init/additional_before/quicksearch.phtml`
```javascript
// Modify the number of suggestions returned
klevu.search.quick.getScope().chains.request.control.addAfter("initRequest", {
    name: "modifyQuickQuery",
    fire: function (data, scope) {
        klevu.search.modules.overrideSearchTerm(data, scope);
    }
});
```
Alternatively, you can change the rewritten query string by modifying the following section of the same file
```javascript
klevu.each(data.request.current.recordQueries,function(key, query) {
    klevu.setObjectPath(
        data,
        "localOverrides.query." + query.id + ".settings.limit",
        15 // The maximum number of suggestions to be returned
    );
});
```

Modifying the Minimum Number of Characters for Quick Search Suggestions
-----------------------------------------------------------------------

The following files are involved in this customisation

* [etc/di.xml](etc/di.xml)
* [Service/InteractiveOptionsProvider.php](Service/InteractiveOptionsProvider.php)

This customisation changes the minimum number of characters required to trigger quick search suggestions
from the default of "0" to "5".  
This is an example of overriding a core setting already present in the `klevu.interactive` call.

To implement, we create the [InteractiveOptionsProvider](Service/InteractiveOptionsProvider.php) to send
configuration settings to be merged with those defined by the core extensions.  
The relevant return value is
```php
return [
    'search' => [
        // Set the minimum number of characters before quick search suggestions appear
        'minChars' => 5,
    ],
];
```

In this class we also inject and query an instance of `Klevu\FrontendJs\Api\IsEnabledConditionInterface` to
ensure we only provide our customisations to the core Klevu modules if Search is enabled on your store and you
are using ThemeV2.

This provider is registered in [`etc/di.xml`](etc/di.xml). Here we also inject the `IsEnabledCondition` from  the
`Klevu_Search` module so our changes are output under the same conditions as the core settings.  
You can create your own IsEnabledCondition to use here by implementing the `Klevu\FrontendJs\Api\IsEnabledConditionInterface`
class, but that is out of scope for these examples.

To disable this customisation, simply delete the `search` array from the options provider's return.

Implementing a Custom Template for Quick Search Suggestions
-----------------------------------------------------------

The following files are involved in this customisation

* [etc/di.xml](etc/di.xml)
* [Service/InteractiveOptionsProvider.php](Service/InteractiveOptionsProvider.php)
* [view/frontend/layout/default_head_blocks.xml](view/frontend/layout/default_head_blocks.xml)
* [view/frontend/templates/html/head/js_init/additional_before/quicksearch.phtml](view/frontend/templates/html/head/js_init/additional_before/quicksearch.phtml)
* [view/frontend/templates/klevu/quick_product_block.phtml](view/frontend/templates/klevu/quick_product_block.phtml)

This customisation sets a custom template for the quick search suggestions element, which prepends the string
"!CUSTOMISED!" before each product name.
NOTE: You will need to disable the Quick Search API call customisation above to see this change.

To implement, we initially create the [InteractiveOptionsProvider](Service/InteractiveOptionsProvider.php) to defer
powering up the quick search functionality.  
The relevant return value is
```php
return [
    'powerUp' => [
        // Defer quick search power up
        'quick' => false,
    ],
];
```
In this class we also inject and query an instance of `Klevu\FrontendJs\Api\IsEnabledConditionInterface` to
ensure we only provide our customisations to the core Klevu modules if Search is enabled on your store and you
are using ThemeV2.

In addition to providing power up configuration, we add some JavaScript to the `klevu.interactive` call by
creating the template [`view/frontend/templates/html/head/js_init/additional_before/quicksearch.phtml`](view/frontend/templates/html/head/js_init/additional_before/quicksearch.phtml)
and inserting into each page via the layout instructions in [`view/frontend/layout/default_head_blocks.xml`](view/frontend/layout/default_head_blocks.xml).

Within this file, the `klevu.coreEvent.build({` section is shared with the API call override above, while the
`klevu.coreEvent.attach("setRemoteConfigQuickOverride", {` section contains specific code for both this change
(defining the template change) and the API call override.

The final file created - [`view/frontend/templates/klevu/quick_product_block.phtml`](view/frontend/templates/klevu/quick_product_block.phtml)
contains the new markup used for the products section of the suggestions box.

To disable this customisation, comment out the following section in `view/frontend/templates/html/head/js_init/additional_before/quicksearch.phtml`
```javascript
// Override the klevuQuickProductBlock template with our own
klevu.each(klevu.search.extraSearchBox, function (key, box) {
    box.getScope().template.setTemplate(klevu.dom.helpers.getHTML("#klevuQuickProductBlockCustom"), "klevuQuickProductBlock", true);
});
```

Implementing a Custom Template for the Search Results Landing Page
------------------------------------------------------------------

The following files are involved in this customisation

* [etc/di.xml](etc/di.xml)
* [Service/InteractiveOptionsProvider.php](Service/InteractiveOptionsProvider.php)
* [view/frontend/layout/search_index_index.xml](view/frontend/layout/search_index_index.xml)
* [view/frontend/templates/html/head/js_init/additional_before/search_results_page.phtml](view/frontend/templates/html/head/js_init/additional_before/search_results_page.phtml)
* [view/frontend/templates/klevu/search_results_page.phtml](view/frontend/templates/klevu/search_results_page.phtml)

This customisation sets a custom template for the results count heading of the search results page, prepending the string
"!CUSTOMISED!".

To implement, we initially create the [InteractiveOptionsProvider](Service/InteractiveOptionsProvider.php) to defer
powering up the search results landing page functionality.  
The relevant return value is
```php
return [
    'powerUp' => [
        // Defer the search results landing page power up
        'landing' => false,
    ],
];
```
In this class we also inject and query an instance of `Klevu\FrontendJs\Api\IsEnabledConditionInterface` to
ensure we only provide our customisations to the core Klevu modules if Search is enabled on your store and you
are using ThemeV2.

In addition to providing power up configuration, we add some JavaScript to the `klevu.interactive` call by
creating the template [`view/frontend/templates/html/head/js_init/additional_before/search_results_page.phtml`](view/frontend/templates/html/head/js_init/additional_before/search_results_page.phtml)
and inserting into the Klevu-powered search results page (/search) via the layout instructions in 
[`view/frontend/layout/search_index_index.xml`](view/frontend/layout/search_index_index.xml).

The final file created - [`view/frontend/templates/klevu/search_results_page.phtml`](view/frontend/templates/klevu/search_results_page.phtml) - 
contains the new markup used for the SRLP results count heading.

To disable this customisation, comment out the following section in `view/frontend/templates/html/head/js_init/additional_before/search_results_page.phtml`
```javascript
// Override the klevuLandingTemplateResultsHeadingTitle template with our own
klevu.search.landing.getScope().template.setTemplate(
    klevu.dom.helpers.getHTML("#klevuLandingTemplateResultsHeadingTitleCustom"),
    "klevuLandingTemplateResultsHeadingTitle",
    true
);
```
Note: you can also remove all the instructions in `search_index_index,xml`, but ensure you _also_ remove the corresponding
`powerUp` deferral in the `InteractiveOptionsProvider`.
