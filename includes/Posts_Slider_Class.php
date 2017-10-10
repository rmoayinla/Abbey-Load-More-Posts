<?php
/**
 * 
 * A Post slider class for loading posts in a carousel like slide
 *
 * this post slider only works with Abbey theme, since the functions here and hooks can only be found in Abbey theme
 * post slide are displayed at the top in archive pages i.e. category, author and blog 
 * only one (1) post is showed at loadtime, the next posts are loaded via AJAX 
 *
 *@author: Rabiu Mustapha
 *@version: 1.0
 *@package: Abbey Ajax Load More Posts plugin 
 *
 */

class Abbey_Posts_Slider{

 	/**
 	 * Array of posts that should be excluded when loading via Ajax 
 	 * these posts will be pass to the query var 'post__not_in' index to exclude them in the query
 	 *@var: array 	$post_ids 	arrays of posts to be excluded in the query 
 	 */
 	private $post_ids = []; 

 	
 	/**
 	 * Class constructor: calls when the class is instantiated 
 	 */
 	public function __construct(){

 		/**
 		 * hook to Abbey theme archive slide action hook 
 		 * this displays a wordpress post in a slide carousel 
 		 */
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

 	/**
 	 * Display a single post in a slick carousel
 	 * the post to be displayed is not queried from the database, but gotten from global $wp_query->posts
 	 * only one post is displayed here, the post is displayed using a plugin template partial 
 	 *@param: 	array 	$posts 		array of posts from $wp_query 
 	 */
 	function archive_slide( $posts ){ 
 		global $wp_query; //wp global container which stores query result //

 		//incase the passed $posts param is empty, get it from global $wp_query //
 		if( empty( $posts ) ) $posts = $wp_query->posts;

 		//bail if we have only 1 post or an empty post //
 		if( empty( $posts ) || count( $posts ) < 2 ) return; 

 		global $post_to_load; // the container to store the post to load in slide //
 		$post_to_load = $posts[ 0 ]; //copy only the 1st post to our container //

 		setup_postdata( $post_to_load ); //setup the wp template tags with our post data //
 		?>
 		<!-- load the template partial which contains the actual markup for displaying the slide -->
 		<?php load_template( ABBEY_LOAD_POSTS_PLUGIN_DIR."partials/post-slide.php", false ); ?>

 		<?php wp_reset_postdata(); //reset our post data back to the main query //

 	}

 	/**
 	 * Load posts in slick slider using AJAX 
 	 * this is a way of lazyloading posts, 
 	 * calls wp_die after outputting to stop execution of any other scripts 
 	 */
 	public function load_posts(){
 		//our containers for storing query vars and slide post for the actual query //
 		 $query_vars = $slide_post = null;

 		 //copy the query vars from the $_POST object //
 		 if( !empty( $_POST[ "slide_posts_query_vars" ] ) ) $query_vars = $_POST[ "slide_posts_query_vars" ];
 		
 		 //only load one page //
 		 $query_vars[ 'posts_per_page' ] = 1; 

 		 // remove the paged parameter, so query is run for all pages //
 		 $query_vars[ 'paged' ] = '';

 		 //add posts to be excluded for this query //
 		 if( !empty( $_POST[ 'posts_to_exclude' ] ) ) $query_vars[ 'post__not_in' ] = (array)$_POST[ 'posts_to_exclude' ];

 		 $slide_post = new WP_Query( $query_vars );

 		 /**
 		  * If we the query returned empty post, bail with a message 
 		  */
 		 if( !$slide_post->have_posts() ){
 		 	echo sprintf( '<div class="no-post-slide"><p>%s</p></div>', __( "No posts found", "abbey-ajax-load-posts" ) );
 		 	wp_die(); //terminate the script //
 		 }

 		 // start our query loop //
 		 while( $slide_post->have_posts() ) : $slide_post->the_post(); ?>
 		 	
 		 	<!-- load the template partial containing the markup for our slide post -->
 		 	<?php load_template( ABBEY_LOAD_POSTS_PLUGIN_DIR."partials/post-slide.php", false ); ?>
 		 
 		 <?php endwhile; wp_reset_postdata(); //reset back to the main query post data //
 		 
 		wp_die(); //terminate the script //
 	}


 }