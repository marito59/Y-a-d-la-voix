<?php

/**
 * The template part for displaying content
 *
 * @package WordPress
 * @subpackage Visual Composer Starter
 * @since Visual Composer Starter 1.0
 */


?>

<article class="page-summary">
	<header class="entry-header">
		<h2 class="entry-title"><?php the_title(); ?></h2>
	</header>
	<span style="width:300px; float: left; padding-right:20px;">
		<?php the_post_thumbnail( ); ?>
	</span>
	<span class="entry-content">
		<?php the_content(); ?>
	</span>
</article>
