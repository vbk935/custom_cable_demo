<?php
/**
 * Array of plugin options
 */

$options = array();

$options['general'][] = array(
    "name" => __( "Main Settings", "aws" ),
    "type" => "heading"
);

$options['general'][] = array(
    "name"  => __( "Seamless integration", "aws" ),
    "desc"  => __( "Replace all the standard search forms on your website ( may not work with some themes ).", "aws" ),
    "id"    => "seamless",
    "value" => 'false',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false'  => __( 'Off', 'aws' ),
    )
);

$options['general'][] = array(
    "name"  => __( "Cache results", "aws" ),
    "desc"  => __( "Turn off if you have old data in the search results after content of products was changed.<br><strong>CAUTION:</strong> can dramatically increase search speed", "aws" ),
    "id"    => "cache",
    "value" => 'true',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false'  => __( 'Off', 'aws' ),
    )
);

$options['general'][] = array(
    "name"  => __( "Sync index table", "aws" ),
    "desc"  => __( "Automatically update plugin index table when product content was changed. This means that in search there will be always latest product data.", "aws" ) . '<br>' .
               __( "Turn this off if you have any problems with performance.", "aws" ),
    "id"    => "autoupdates",
    "value" => 'true',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false'  => __( 'Off', 'aws' ),
    )
);

$options['general'][] = array(
    "name"  => __( "Search in", "aws" ),
    "desc"  => __( "Search source: Drag&drop sources to activate or deactivate them.", "aws" ),
    "id"    => "search_in",
    "value" => "title,content,sku,excerpt",
    "choices" => array( "title", "content", "sku", "excerpt", "category", "tag" ),
    "type"  => "sortable"
);

$options['general'][] = array(
    "name"  => __( "Show out-of-stock", "aws" ),
    "desc"  => __( "Show out-of-stock products in search", "aws" ),
    "id"    => "outofstock",
    "value" => 'true',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'Show', 'aws' ),
        'false'  => __( 'Hide', 'aws' ),
    )
);

$options['general'][] = array(
    "name"  => __( "Stop words list", "aws" ),
    "desc"  => __( "Comma separated list of words that will be excluded from search.", "aws" ) . '<br>' . __( "Re-index required on change.", "aws" ),
    "id"    => "stopwords",
    "value" => "a, also, am, an, and, are, as, at, be, but, by, call, can, co, con, de, do, due, eg, eight, etc, even, ever, every, for, from, full, go, had, has, hasnt, have, he, hence, her, here, his, how, ie, if, in, inc, into, is, it, its, ltd, me, my, no, none, nor, not, now, of, off, on, once, one, only, onto, or, our, ours, out, over, own, part, per, put, re, see, so, some, ten, than, that, the, their, there, these, they, this, three, thru, thus, to, too, top, un, up, us, very, via, was, we, well, were, what, when, where, who, why, will",
    "type"  => "textarea"
);

$options['general'][] = array(
    "name"  => __( "Use Google Analytics", "aws" ),
    "desc"  => __( "Use google analytics to track searches. You need google analytics to be installed on your site.", "aws" ) . '<br>' . __( "Will send event with category - 'AWS search', action - 'AWS Search Term' and label of value of search term.", "aws" ),
    "id"    => "use_analytics",
    "value" => 'false',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false'  => __( 'Off', 'aws' ),
    )
);

// Search Form Settings
$options['form'][] = array(
    "name"  => __( "Text for search field", "aws" ),
    "desc"  => __( "Text for search field placeholder.", "aws" ),
    "id"    => "search_field_text",
    "value" => __( "Search", "aws" ),
    "type"  => "text"
);

$options['form'][] = array(
    "name"  => __( "Text for show more button", "aws" ),
    "desc"  => __( "Text for link to search results page at the bottom of search results block.", "aws" ),
    "id"    => "show_more_text",
    "value" => __( "View all results", "aws" ),
    "type"  => "text"
);

$options['form'][] = array(
    "name"  => __( "Nothing found field", "aws" ),
    "desc"  => __( "Text when there is no search results.", "aws" ),
    "id"    => "not_found_text",
    "value" => __( "Nothing found", "aws" ),
    "type"  => "textarea"
);

$options['form'][] = array(
    "name"  => __( "Minimum number of characters", "aws" ),
    "desc"  => __( "Minimum number of characters required to run ajax search.", "aws" ),
    "id"    => "min_chars",
    "value" => 1,
    "type"  => "number"
);

$options['form'][] = array(
    "name"  => __( "Show loader", "aws" ),
    "desc"  => __( "Show loader animation while searching.", "aws" ),
    "id"    => "show_loader",
    "value" => 'true',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false' => __( 'Off', 'aws' ),
    )
);

$options['form'][] = array(
    "name"  => __( "Show clear button", "aws" ),
    "desc"  => __( "Show 'Clear search string' button for desktop devices ( for mobile it is always visible ).", "aws" ),
    "id"    => "show_clear",
    "value" => 'true',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false' => __( 'Off', 'aws' ),
    )
);

$options['form'][] = array(
    "name"  => __( "Show 'View All Results'", "aws" ),
    "desc"  => __( "Show link to search results page at the bottom of search results block.", "aws" ),
    "id"    => "show_more",
    "value" => 'false',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false' => __( 'Off', 'aws' )
    )
);

$options['form'][] = array(
    "name"  => __( "Search Results", "aws" ),
    "desc"  => __( "Choose how to view search results.", "aws" ),
    "id"    => "show_page",
    "value" => 'false',
    "type"  => "radio",
    'choices' => array(
        'true'     => __( 'Both ajax search results and search results page', 'aws' ),
        'false'    => __( 'Only ajax search results ( no search results page )', 'aws' ),
        'ajax_off' => __( 'Only search results page ( no ajax search results )', 'aws' )
    )
);

$options['form'][] = array(
    "name"  => __( "Form Styling", "aws" ),
    "desc"  => __( "Choose search form layout", "aws" ) . '<br>' . __( "Filter button will be visible only if you have more than one active filter for current search form instance.", "aws" ),
    "id"    => "buttons_order",
    "value" => '1',
    "type"  => "radio-image",
    'choices' => array(
        '1' => 'btn-layout1.png',
        '2' => 'btn-layout2.png',
        '3' => 'btn-layout3.png',
    )
);


// Search Results Settings

$options['results'][] = array(
    "name"  => __( "Description source", "aws" ),
    "desc"  => __( "From where to take product description.<br>If first source is empty data will be taken from other sources.", "aws" ),
    "id"    => "desc_source",
    "value" => 'content',
    "type"  => "radio",
    'choices' => array(
        'content'  => __( 'Content', 'aws' ),
        'excerpt'  => __( 'Excerpt', 'aws' ),
    )
);

$options['results'][] = array(
    "name"  => __( "Description length", "aws" ),
    "desc"  => __( "Maximal allowed number of words for product description.", "aws" ),
    "id"    => "excerpt_length",
    "value" => 20,
    "type"  => "number"
);

$options['results'][] = array(
    "name"  => __( "Max number of results", "aws" ),
    "desc"  => __( "Maximum number of displayed search results.", "aws" ),
    "id"    => "results_num",
    "value" => 10,
    "type"  => "number"
);

$options['results'][] = array(
    "name"    => __( "View", "aws" ),
    "type"    => "heading"
);

$options['results'][] = array(
    "name"  => __( "Show image", "aws" ),
    "desc"  => __( "Show product image for each search result.", "aws" ),
    "id"    => "show_image",
    "value" => 'true',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false'  => __( 'Off', 'aws' ),
    )
);

$options['results'][] = array(
    "name"  => __( "Show description", "aws" ),
    "desc"  => __( "Show product description for each search result.", "aws" ),
    "id"    => "show_excerpt",
    "value" => 'true',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false'  => __( 'Off', 'aws' ),
    )
);

$options['results'][] = array(
    "name"  => __( "Description content", "aws" ),
    "desc"  => __( "What to show in product description?", "aws" ),
    "id"    => "mark_words",
    "value" => 'true',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( "Smart scrapping sentences with searching terms from product description.", "aws" ),
        'false' => __( "First N words of product description ( number of words that you choose below. )", "aws" ),
    )
);

$options['results'][] = array(
    "name"  => __( "Show price", "aws" ),
    "desc"  => __( "Show product price for each search result.", "aws" ),
    "id"    => "show_price",
    "value" => 'true',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false' => __( 'Off', 'aws' ),
    )
);

$options['results'][] = array(
    "name"  => __( "Show price for out of stock", "aws" ),
    "desc"  => __( "Show product price for out of stock products.", "aws" ),
    "id"    => "show_outofstock_price",
    "value" => 'true',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false' => __( 'Off', 'aws' ),
    )
);

$options['results'][] = array(
    "name"  => __( "Show categories archive", "aws" ),
    "desc"  => __( "Include categories archives pages to search result.", "aws" ),
    "id"    => "show_cats",
    "value" => 'false',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false' => __( 'Off', 'aws' ),
    )
);

$options['results'][] = array(
    "name"  => __( "Show tags archive", "aws" ),
    "desc"  => __( "Include tags archives pages to search results.", "aws" ),
    "id"    => "show_tags",
    "value" => 'false',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false' => __( 'Off', 'aws' ),
    )
);

$options['results'][] = array(
    "name"  => __( "Show sale badge", "aws" ),
    "desc"  => __( "Show sale badge for products in search results.", "aws" ),
    "id"    => "show_sale",
    "value" => 'true',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false' => __( 'Off', 'aws' ),
    )
);

$options['results'][] = array(
    "name"  => __( "Show product SKU", "aws" ),
    "desc"  => __( "Show product SKU in search results.", "aws" ),
    "id"    => "show_sku",
    "value" => 'false',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false' => __( 'Off', 'aws' ),
    )
);

$options['results'][] = array(
    "name"  => __( "Show stock status", "aws" ),
    "desc"  => __( "Show stock status for every product in search results.", "aws" ),
    "id"    => "show_stock",
    "value" => 'false',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false' => __( 'Off', 'aws' ),
    )
);

$options['results'][] = array(
    "name"  => __( "Show featured icon", "aws" ),
    "desc"  => __( "Show or not star icon for featured products.", "aws" ),
    "id"    => "show_featured",
    "value" => 'false',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false' => __( 'Off', 'aws' ),
    )
);