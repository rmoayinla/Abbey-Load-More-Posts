<?php 
/*
* Plugin Name: Abbey Ajax Load More Posts
* Plugin URI: 
* Description: Use this plugin with abbey theme to load posts by Ajax 
* Author: Rabiu Mustapha
* Author URI: 
* Version: 0.1
* Text Domain: abbey-ajax-load-posts
* Github Plugin URI: 
*/


if ( ! defined( 'ABSPATH' ) ) exit; 

if ( !defined( 'ABBEY_LOAD_POSTS_PLUGIN_DIR' ) ) 
	define( 'ABBEY_LOAD_POSTS_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

if ( !defined( 'ABBEY_LOAD_POSTS_PLUGIN_URL' ) ) 
	define( 'ABBEY_LOAD_POSTS_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

require_once ABBEY_LOAD_POSTS_PLUGIN_DIR."bootstrap.php";

if( class_exists( "Abbey_Ajax_Load_Posts" ) )
	new Abbey_Ajax_Load_Posts();

if( class_exists( "Abbey_Posts_Slider" ) )
	new Abbey_Posts_Slider(); 
