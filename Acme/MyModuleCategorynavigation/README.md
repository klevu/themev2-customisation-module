Klevu ThemeV2 Customisation Examples
====================================

This demonstration module provides a "jumping off point" for creating your own
custom extension for JSv2 customisations.  
You can install this package out of the box to see examples of the following functionality

* Implementing a custom template for Klevu-powered category pages (to prepend a string to the results returned header)

Implementing a Custom Template for Klevu-Powered Category Pages
---------------------------------------------------------------

The following files are involved in this customisation

* [etc/di.xml](etc/di.xml)
* [Service/InteractiveOptionsProvider.php](Service/InteractiveOptionsProvider.php)
* [view/frontend/layout/klevu_category_index.xml](view/frontend/layout/klevu_category_index.xml)
* [view/frontend/templates/html/head/js_init/additional_before/category_page.phtml](view/frontend/templates/html/head/js_init/additional_before/category_page.phtml)
* [view/frontend/templates/klevu/category_page.phtml](view/frontend/templates/klevu/category_page.phtml)

This customisation sets a custom template for the results count heading for category pages, prepending the string "!CUSTOMISED!"

To implement, we initially create the [InteractiveOptionsProvider](Service/InteractiveOptionsProvider.php) to defer
powering up the category page functionality.  
The relevant return value is
```php
return [
    'powerUp' => [
        // Defer smart category merchandising power up
        'catnav' => false,
    ],
];
```
In this class we also inject and query an instance of `Klevu\FrontendJs\Api\IsEnabledConditionInterface` to
ensure we only provide our customisations to the core Klevu modules if Smart Category Merchandising is enabled 
on your store and you are using ThemeV2.

In addition to providing power up configuration, we add some JavaScript to the `klevu.interactive` call by
creating the template [`view/frontend/templates/html/head/js_init/additional_before/category_page.phtml`](view/frontend/templates/html/head/js_init/additional_before/category_page.phtml)
and inserting into the Klevu-powered category pages via the layout instructions in
[`view/frontend/layout/klevu_category_index.xml`](view/frontend/layout/klevu_category_index.xml).


The final file created = [`view/frontend/templates/klevu/category_page.phtml`](view/frontend/templates/category_page.phtml) -
contains the new markup used for the results count heading.

To disable this customisation, comment out the following section in `view/frontend/templates/html/head/js_init/additional_before/category_page.phtml`
```javascript
// Override the klevuLandingTemplateResultsHeadingTitle template with our own
klevu.search.catnav.getScope().template.setTemplate(
    klevu.dom.helpers.getHTML("#klevuLandingTemplateResultsHeadingTitleCustom"),
    "klevuLandingTemplateResultsHeadingTitle",
    true
);
```

Note: you can also remove all the instruction in `klevu_category_index,xml`, but ensure you _also_ remove the corresponding
`powerUp` deferral in the `InteractiveOptionsProvider`
