<?php

/**
 * The template part for displaying content
 *
 * @package WordPress
 * @subpackage Visual Composer Starter
 * @since Visual Composer Starter 1.0
 */


?>

<li <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<small><?php the_time('j F Y') ?></small>
	<h5><a href="<?php the_permalink() ?>" rel="bookmark" title="Lien permanent vers <?php the_title_attribute(); ?>">
		<?php the_title(); ?>
	</a></h5>
	<div class="entry">
		<?php the_content('[ Lire la suite ]'); ?>
	</div>
</li>
