<?php
global $count;
$more_link = sprintf( '<a href="%1$s" title="%2$s" class="excerpt-link">%3$s</a>', 
						get_permalink(), 
						esc_attr__( "Read full article", "abbey" ), 
						esc_html__( "Read more", "abbey" ) 
					);
?>
		<article class="post-panel post-count-<?php echo $count; ?>" >
			<?php if ( $count === 1 ) : ?>
				<div class="post-thumbnail"><?php abbey_page_media( "large" ); ?></div>
			<?php endif;  ?>

			<header class="post-panel-heading">
				<div class="row margin-minus-bottom-sm">
					<ul class="top-post-info">
						<li><?php abbey_show_post_type(); ?> </li>
						<li> <?php echo human_time_diff( get_the_time("U"), current_time("timestamp") ); ?> </li>
					</ul>
				</div>

				<?php echo sprintf( '<h2><a href="%1$s" title="%2$s">%3$s</a></h2>', 
									get_permalink(), 
									esc_attr__( "Read this article", "abbey" ), 
									get_the_title()
								); ?>

				<ul class="breadcrumb post-info"><?php abbey_post_info( true, array( "author", "date" ) ); ?></ul>
			</header>
			
			<?php if( $count > 1 ) :  ?>
				<div class="post-thumbnail"><?php abbey_page_media( "large" ); ?></div>
			<?php endif; ?>
				
			<div class="post-excerpts"><?php abbey_excerpt( "", $more_link, true ); ?></div>
			
			<footer class="post-panel-footer">
				<ul class="list-inline no-list-style post-panel-info">
					<li><?php echo abbey_cats_or_tags( "categories", "", "fa-folder-o" ); ?></li>
					<li><?php echo abbey_cats_or_tags( "tags", "", "fa-tags" );  ?></li>
				</ul>
			</footer>

			
			<div class="post-panel-metas"><?php do_action( "abbey_archives_post_panel_metas", $count ); ?></div>
			
		</article><!--.post-panel closes-->


