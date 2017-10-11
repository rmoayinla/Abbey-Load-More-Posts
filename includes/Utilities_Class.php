<?php 
/**
 * Simple class that provide utilities function for my plgin 
 * methods from these class can be called anywhere in the theme 
 * @since: 0.1
 * @package: Abbey Load More Posts plugin 
 * @category: includes
 * @author: Rabiu Mustapha 
 */

class Abbey_Load_Posts_Utilities{
	
	static function query_args(){
		/**
		 * check if we are not on an archive page
		 * this plugin is meant to do nothing on Front page, Single pages
		 * so simply bail when the current page is not an archive page 
		 */
		if( is_single() || is_singular() || is_front_page() )
			return;

		$query = "";
		
		/**
		 * wordpress global container for query information
		 * this variable holds  the result of wordpress post query 
		 * query_vars, posts, max_num_page, found_posts etc are stored in this variable 
		 * I will be copying some vars from this variable to load the next page post 
		 */
		global $wp_query;

		/**
		 * start copying what we need, the query_vars, max_num_pages etc
		 */
		$query = $wp_query->query_vars;
		$query[ "max_num_pages" ] = $wp_query->max_num_pages;

		//remove nopaging index if set //
		if( isset( $query[ "nopaging" ] ) ) unset( $query[ "nopaging" ] );
		
		//return the query var //
		return $query;

	}

	/**
	 * method to setup the args to how I really want to use it 
	 * the query_args() methods return too many vars, and some of them are not needed in my custom query
	 * this method filters out only the args I need which will be passed to the AJAX script to load next posts 
	 *@return: (array) 		$query_args 		an array of filtered args that will be passed to Ajax script 
	 */
	static function setup_query_args(){
		$args = self::query_args();
		$query_args = [];
			if( !empty( $args ) ){ 
				$query_args[ "paged" ] = $args[ "paged" ] > 0 ? $args[ "paged" ] : 1;
				$query_args[ "suppress_filters" ] = true;
				$query_args[ "category_name" ] = $args[ "category_name" ]; 
				$query_args[ "cat" ] = $args[ "cat" ];
				$query_args[ "posts_per_page" ] = $args[ "posts_per_page" ];
				$query_args[ "post_type" ] = $args[ "post_type" ];
				$query_args[ "author" ] = $args[ "author" ]; 
				$query_args[ "author_name" ] = $args[ "author_name" ];
				$query_args[ "tag" ] = $args[ "tag" ]; 
				$query_args[ "tag_id" ] = $args[ "tag_id" ];
				$query_args[ "max_num_pages" ] = $args[ "max_num_pages" ];

					
			}
			//merge and replace the query vars and return it //
			return wp_parse_args( $query_args, $args ); 
	}

}


