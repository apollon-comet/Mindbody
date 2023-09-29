<?php
/**
 * JS libraries.
 *
 * @version 1.0
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

return array(
	'autocomplete'           => array(
		array(
			'title'      => esc_html__( 'Autocomplete', 'xts-theme' ),
			'name'       => 'autocomplete',
			'file'       => '/js/libs/autocomplete',
			'in_footer'  => true,
			'dependency' => array(),
		),
	),
	'cookie'                 => array(
		array(
			'title'      => esc_html__( 'Cookie', 'xts-theme' ),
			'name'       => 'cookie',
			'file'       => '/js/libs/cookie',
			'in_footer'  => true,
			'dependency' => array(),
		),
	),
	'countdown-bundle'       => array(
		array(
			'title'      => esc_html__( 'Countdown', 'xts-theme' ),
			'name'       => 'countdown-bundle',
			'file'       => '/js/libs/countdown-bundle',
			'in_footer'  => true,
			'dependency' => array(),
		),
	),
	'device'                 => array(
		array(
			'title'      => esc_html__( 'Device', 'xts-theme' ),
			'name'       => 'device',
			'file'       => '/js/libs/device',
			'in_footer'  => false,
			'dependency' => array( 'jquery' ),
		),
	),
	'isotope-bundle'         => array(
		array(
			'title'      => esc_html__( 'Isotope', 'xts-theme' ),
			'name'       => 'isotope-bundle',
			'file'       => '/js/libs/isotope-bundle',
			'in_footer'  => true,
			'dependency' => array(),
		),
	),
	'justified'              => array(
		array(
			'title'      => esc_html__( 'Justified gallery', 'xts-theme' ),
			'name'       => 'justified',
			'file'       => '/js/libs/justifiedGallery',
			'in_footer'  => true,
			'dependency' => array(),
		),
	),
	'magnific'               => array(
		array(
			'title'      => esc_html__( 'Magnific popup', 'xts-theme' ),
			'name'       => 'magnific',
			'file'       => '/js/libs/magnific-popup',
			'in_footer'  => true,
			'dependency' => array(),
		),
	),
	'owl'                    => array(
		array(
			'title'      => esc_html__( 'OWL carousel', 'xts-theme' ),
			'name'       => 'owl',
			'file'       => '/js/libs/owl.carousel',
			'in_footer'  => true,
			'dependency' => array(),
		),
	),
	'panr-parallax-bundle'   => array(
		array(
			'title'      => esc_html__( 'Panr parallax', 'xts-theme' ),
			'name'       => 'panr-parallax-bundle',
			'file'       => '/js/libs/panr-parallax-bundle',
			'in_footer'  => true,
			'dependency' => array(),
		),
	),
	'parallax'               => array(
		array(
			'title'      => esc_html__( 'Parallax', 'xts-theme' ),
			'name'       => 'parallax',
			'file'       => '/js/libs/parallax',
			'in_footer'  => true,
			'dependency' => array(),
		),
	),
	'parallax-scroll-bundle' => array(
		array(
			'title'      => esc_html__( 'Parallax scroll', 'xts-theme' ),
			'name'       => 'parallax-scroll-bundle',
			'file'       => '/js/libs/parallax-scroll-bundle',
			'in_footer'  => true,
			'dependency' => array(),
		),
	),
	'photoswipe-bundle'      => array(
		array(
			'title'      => esc_html__( 'Photoswipe', 'xts-theme' ),
			'name'       => 'photoswipe-bundle',
			'file'       => '/js/libs/photoswipe-bundle',
			'in_footer'  => true,
			'dependency' => array(),
		),
	),
	'pjax'                   => array(
		array(
			'title'      => esc_html__( 'PJAX', 'xts-theme' ),
			'name'       => 'pjax',
			'file'       => '/js/libs/pjax',
			'in_footer'  => true,
			'dependency' => array(),
		),
	),
	'slick'                  => array(
		array(
			'title'      => esc_html__( 'Slick slider', 'xts-theme' ),
			'name'       => 'slick',
			'file'       => '/js/libs/slick',
			'in_footer'  => true,
			'dependency' => array(),
		),
	),
	'sticky-kit'             => array(
		array(
			'title'      => esc_html__( 'Sticky kit', 'xts-theme' ),
			'name'       => 'sticky-kit',
			'file'       => '/js/libs/sticky-kit',
			'in_footer'  => true,
			'dependency' => array(),
		),
	),
	'threesixty'             => array(
		array(
			'title'      => esc_html__( 'Threesixty', 'xts-theme' ),
			'name'       => 'threesixty',
			'file'       => '/js/libs/threesixty',
			'in_footer'  => true,
			'dependency' => array(),
		),
	),
	'tooltips'                => array(
		array(
			'title'      => esc_html__( 'Tooltips', 'xts-theme' ),
			'name'       => 'tooltips',
			'file'       => '/js/libs/tooltips',
			'in_footer'  => true,
			'dependency' => array(),
		),
	),
	'vivus'                  => array(
		array(
			'title'      => esc_html__( 'Vivus', 'xts-theme' ),
			'name'       => 'vivus',
			'file'       => '/js/libs/vivus',
			'in_footer'  => true,
			'dependency' => array(),
		),
	),
	'waypoints'               => array(
		array(
			'title'      => esc_html__( 'Waypoint', 'xts-theme' ),
			'name'       => 'waypoints',
			'file'       => '/js/libs/waypoints',
			'in_footer'  => true,
			'dependency' => array(),
		),
	),
);
