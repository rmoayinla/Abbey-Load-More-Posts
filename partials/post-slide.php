<?php 
global $post_to_load;
?>
<aside class="archive-post-slide">
	<div class="row no-inner-margin">
		<figure class="post-slide-thumbnail col-md-4 no-inner-padding col-xs-3">
			<?php abbey_page_media( "large" ); ?>
		</figure>

		<div class="post-slide-body col-md-8 col-xs-9">
			<h4 class="post-slide-title"> <?php the_title(); ?></h4>
			<summary class="post-slide-excerpt"> 
				<?php echo wp_trim_words( get_the_content(), 55, "..." ); ?>
			</summary>
			<a class="btn btn-link slick-add" href="#" data-post-id= "<?php the_ID(); ?>">
				<span class="glyphicon glyphicon-chevron-right"></span>
			</a>
			<a class="btn btn-link"><span class="glyphicon glyphicon-chevron-left"></span></a>
		</div>
	</div>
</aside> 