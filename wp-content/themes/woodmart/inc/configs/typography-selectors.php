<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );}

/**
 * ------------------------------------------------------------------------------------------------
 * Elements selectors for advanced typography options
 * ------------------------------------------------------------------------------------------------
 */

return apply_filters(
	'woodmart_typography_selectors',
	array(
		'main_nav'                           => array(
			'title' => 'Main navigation',
		),
		'main_navigation'                    => array(
			'title'          => 'Main navigation links',
			'selector'       => '.wd-nav.wd-nav-main > li > a',
			'selector-hover' => '.wd-nav.wd-nav-main > li:hover > a, .wd-nav.wd-nav-main > li.current-menu-item > a',
		),
		'mega_menu_drop_first_level'         => array(
			'title'          => 'Menu dropdowns first level',
			'selector'       => 'body .wd-dropdown-menu.wd-design-sized .wd-sub-menu > li > a, body .wd-dropdown-menu.wd-design-full-width .wd-sub-menu > li > a',
			'selector-hover' => 'body .wd-dropdown-menu.wd-design-sized .wd-sub-menu > li > a:hover, body .wd-dropdown-menu.wd-design-full-width .wd-sub-menu > li > a:hover',
		),
		'mega_menu_drop_second_level'        => array(
			'title'          => 'Menu dropdowns second level',
			'selector'       => '.wd-dropdown-menu.wd-design-sized .wd-sub-menu li a, .wd-dropdown-menu.wd-design-full-width .wd-sub-menu li a',
			'selector-hover' => '.wd-dropdown-menu.wd-design-sized .wd-sub-menu li a:hover, .wd-dropdown-menu.wd-design-full-width .wd-sub-menu li a:hover',
		),
		'simple_dropdown'                    => array(
			'title'          => 'Menu links on simple dropdowns',
			'selector'       => '.wd-dropdown-menu.wd-design-default .wd-sub-menu li a',
			'selector-hover' => '.wd-dropdown-menu.wd-design-default .wd-sub-menu li a:hover',
		),
		'secondary_nav'                      => array(
			'title' => 'Other navigations',
		),
		'secondary_navigation'               => array(
			'title'          => 'Secondary navigation links',
			'selector'       => '.wd-nav.wd-nav-secondary > li > a',
			'selector-hover' => '.wd-nav.wd-nav-secondary > li:hover > a, .wd-nav.wd-nav-secondary > li.current-menu-item > a',
		),
		'browse_categories'                  => array(
			'title'          => '"Browse categories" title',
			'selector'       => '.wd-header-cats .menu-opener',
			'selector-hover' => '.wd-header-cats .menu-opener:hover',
		),
		'category_navigation'                => array(
			'title'          => 'Categories navigation links',
			'selector'       => '.wd-dropdown-cats .wd-nav.wd-nav-vertical > li > a',
			'selector-hover' => '.wd-dropdown-cats .wd-nav.wd-nav-vertical > li:hover > a',
		),
		'my_account'                         => array(
			'title'          => 'My account links in the header',
			'selector'       => '.wd-dropdown-my-account .wd-sub-menu li a',
			'selector-hover' => '.wd-dropdown-my-account .wd-sub-menu li a:hover',
		),
		'mobile_nav'                         => array(
			'title' => 'Mobile menu',
		),
		'mobile_menu_first_level'            => array(
			'title'          => 'Mobile menu first level',
			'selector'       => '.wd-nav-mobile > li > a',
			'selector-hover' => '.wd-nav-mobile > li > a:hover, .wd-nav-mobile > li.current-menu-item > a',
		),
		'mobile_menu_second_level'           => array(
			'title'          => 'Mobile menu second level',
			'selector'       => '.wd-nav-mobile .wd-sub-menu li a',
			'selector-hover' => '.wd-nav-mobile .wd-sub-menu li a:hover, .wd-nav-mobile .wd-sub-menu li.current-menu-item > a',
		),
		'page_header'                        => array(
			'title' => 'Page heading',
		),
		'page_title'                         => array(
			'title'    => 'Page title',
			'selector' => '.page-title > .container > .title',
		),
		'page_title_bredcrumps'              => array(
			'title'          => 'Breadcrumbs links',
			'selector'       => '.page-title .breadcrumbs a, .page-title .breadcrumbs span, .page-title .yoast-breadcrumb a, .page-title .yoast-breadcrumb span',
			'selector-hover' => '.page-title .breadcrumbs a:hover, .page-title .yoast-breadcrumb a:hover',
		),
		'products_categories'                => array(
			'title' => 'Products and categories',
		),
		'product_title'                      => array(
			'title'          => 'Product grid title',
			'selector'       => '.product-grid-item .wd-entities-title',
			'selector-hover' => '.product-grid-item .wd-entities-title a:hover',
		),
		'product_price'                      => array(
			'title'    => 'Product grid price',
			'selector' => '.product-grid-item .price > .amount, .product-grid-item .price ins > .amount',
		),
		'product_old_price'                  => array(
			'title'    => 'Product old price',
			'selector' => '.product.product-grid-item del, .product.product-grid-item del .amount',
		),
		'product_category_title'             => array(
			'title'    => 'Category title',
			'selector' => '.product.category-grid-item .wd-entities-title, .product.category-grid-item.cat-design-replace-title .wd-entities-title, .categories-style-masonry-first .category-grid-item:first-child .wd-entities-title',
		),
		'product_category_count'             => array(
			'title'          => 'Category products count',
			'selector'       => '.product.category-grid-item .more-products, .product.category-grid-item.cat-design-replace-title .more-products',
			'selector-hover' => '.product.category-grid-item .more-products a:hover',
		),
		'single_product'                     => array(
			'title' => 'Single product',
		),
		'product_title_single_page'          => array(
			'title'    => 'Single product title',
			'selector' => '.product_title',
		),
		'product_price_single_page'          => array(
			'title'    => 'Single product price',
			'selector' => '.product-image-summary-wrap .summary-inner > .price > .amount, .product-image-summary-wrap .wd-scroll-content > .price > .amount, .product-image-summary-wrap .summary-inner > .price > ins .amount, .product-image-summary-wrap .wd-scroll-content > .price > ins .amount',
		),
		'product_price_old_single_page'      => array(
			'title'    => 'Single product old price',
			'selector' => '.product-image-summary .summary-inner > .price del, .product-image-summary .summary-inner > .price del .amount',
		),
		'product_variable_price_single_page' => array(
			'title'    => 'Variable product price',
			'selector' => '.product-image-summary-wrap .variations_form .woocommerce-variation-price .price > .amount, .product-image-summary-wrap .variations_form .woocommerce-variation-price .price > ins .amount',
		),
		'blog'                               => array(
			'title' => 'Blog',
		),
		'blog_title'                         => array(
			'title'          => 'Blog post title',
			'selector'       => '.post.blog-post-loop .post-title',
			'selector-hover' => '.post.blog-post-loop .post-title a:hover',
		),
		'blog_title_shortcode'               => array(
			'title'          => 'Blog title on WPBakery element',
			'selector'       => '.blog-shortcode .post.blog-post-loop .post-title',
			'selector-hover' => '.blog-shortcode .post.blog-post-loop .post-title a:hover',
		),
		'blog_title_carousel'                => array(
			'title'          => 'Blog title on carousel',
			'selector'       => '.slider-type-post .post.blog-post-loop .post-title',
			'selector-hover' => '.slider-type-post .post.blog-post-loop .post-title a:hover',
		),
		'blog_title_sinle_post'              => array(
			'title'    => 'Blog title on single post',
			'selector' => '.post-single-page .post-title',
		),
		'custom_selector'                    => array(
			'title' => 'Write your own selector',
		),
		'custom'                             => array(
			'title'    => 'Custom selector',
			'selector' => 'custom',
		),
	)
);
