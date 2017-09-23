<?php
 class Abbey_Posts_Slider{

 	private $post_ids = []; 

 	

 	public function __construct(){

 		add_action( "abbey_theme_archive_slide", array( $this, "archive_slide" ) );
 		
 		/**
		 * hook to wordpress ajax action hook
		 * the callable function i.e 2nd parameter is the class method will loading will occur 
		 * the same callable function is hook to 2 different actions, this is a wordpress way of AJax
		 * callable function has to be hooked to wp_ajax_{action_name} and wp_ajax_nopriv_{action_name}
		 *@see: load_posts() for the actual loading of posts 
		 */
		add_action( 'wp_ajax_nopriv_abbey_archive_slide_posts', array( $this, 'load_posts') );
		add_action( 'wp_ajax_abbey_archive_slide_posts', array( $this, 'load_posts' ) );
 	}

 	function archive_slide( $posts ){ 
 		global $wp_query;


 		if( empty( $posts ) ) $posts = $wp_query->posts;

 		//bail if we have only 1 post or an empty post was passed //
 		if( empty( $posts ) || count( $posts ) < 2 ) return; ?>

 		 <?php

 		global $post_to_load;
 		$post_to_load = $posts[ 0 ]; 
 		setup_postdata( $post_to_load );
 		?>

 		<?php load_template( ABBEY_LOAD_POSTS_PLUGIN_DIR."partials/post-slide.php", false ); ?>

 		<?php wp_reset_postdata();

 	}

 	/**
 	 * Load posts in slick slider using AJAX 
 	 * this is a way of lazyloading posts, 
 	 * calls wp_die after outputting to stop execution of any other scripts 
 	 */
 	public function load_posts(){
 		 $query_vars = $slide_post = null;

 		 if( !empty( $_POST[ "slide_posts_query_vars" ] ) ) $query_vars = $_POST[ "slide_posts_query_vars" ];
 		
 		 //only load one page //
 		 $query_vars[ 'posts_per_page' ] = 1; 

 		 // remove the paged parameter, so query is run for all pages //
 		 $query_vars[ 'paged' ] = '';

 		 //add posts to be excluded for this query //
 		 if( !empty( $_POST[ 'posts_to_exclude' ] ) ) $query_vars[ 'post__not_in' ] = (array)$_POST[ 'posts_to_exclude' ];

 		 $slide_post = new WP_Query( $query_vars );

 		 if( !$slide_post->have_posts() ){
 		 	echo sprintf( '<div class="no-post-slide"><p>%s</p></div>', __( "No posts found", "abbey-ajax-load-posts" ) );
 		 	wp_die();
 		 }

 		 while( $slide_post->have_posts() ) : $slide_post->the_post(); ?>
 		 	<?php load_template( ABBEY_LOAD_POSTS_PLUGIN_DIR."partials/post-slide.php", false ); ?>
 		 <?php endwhile; wp_reset_postdata();
 		 
 		wp_die();
 	}


 }