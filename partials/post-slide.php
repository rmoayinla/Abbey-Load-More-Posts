<?php 
global $first_post;
?>
<aside class="archive-post-slide">
	<div class="row no-inner-margin">
		<figure class="post-slide-thumbnail col-md-4">

			<?php if( has_post_thumbnail( $first_post->ID ) ) : ?>
				<?php the_post_thumbnail( $first_post->ID, "large" ); ?>
			<?php else : ?>
				<?php $thumbnail = get_media_embedded_in_content( $first_post->post_content ); ?>
				<?php if( !empty( $thumbnail ) ) echo $thumbnail[0];?>
			<?php endif; ?>

		</figure>

		<div class="post-slide-body col-md-8">
			<h4 class="post-slide-title"> <?php echo apply_filters( "the_title", $first_post->post_title ); ?></h4>
			<summary class="post-slide-excerpt"> 
				<?php echo wp_trim_words( $first_post->post_content, 55, "..." ); ?>
			</summary>
			<a class="btn btn-link slick-add"href="#"><span class="glyphicon glyphicon-chevron-right"></span></a>
			<a class="btn btn-link"><span class="glyphicon glyphicon-chevron-left"></span></a>
		</div>
	</div>
</aside> 