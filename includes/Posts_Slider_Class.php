<?php
 class Abbey_Posts_Slider{

 	public function __construct(){
 		add_action( "abbey_theme_archive_slide", array( $this, "archive_slide" ) );
 		/**
		 * hook to wordpress ajax action hook
		 * the callable function i.e 2nd parameter is the class method will loading will occur 
		 *@see: load_posts() for the actual loading of posts 
		 */
		add_action( 'wp_ajax_nopriv_abbey_archive_slide_posts', array( $this, 'load_posts') );
		add_action( 'wp_ajax_abbey_archive_slide_posts', array( $this, 'load_posts' ) );
 	}

 	function archive_slide( $posts ){ 

 		if( empty( $posts ) || count( $posts ) < 2 ) return; ?>

 		if( empty( $posts ) ) return; ?>


 		 <?php

 		global $first_post;
 		$first_post = $posts[0]; ?>

 		<?php load_template( ABBEY_LOAD_POSTS_PLUGIN_DIR."partials/post-slide.php", false ); ?>

 		<?php 

 	}

 	function load_posts(){
 		echo "<div>Post is now loaded</div>";
 		die();
 	}
 }