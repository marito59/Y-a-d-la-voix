
                                    
<li class="<?php post_class(); ?>"" id="post-<?php the_ID(); ?>">
    <small><?php the_date(); ?></small>
    <h2><a href="<?php the_permalink(); ?>" rel="bookmark" title="Lien permanent vers <?php the_title(); ?>"><?php the_title(); ?></a></h2>
    <div class="entry">
        <?php the_excerpt(); ?>
    </div>
</li>

