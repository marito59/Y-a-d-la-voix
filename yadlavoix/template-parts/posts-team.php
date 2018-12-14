
                                    
<article class="team_member">
    <span class="team_member_pic">
		<?php the_post_thumbnail( ); ?>
	</span>
	<header class="team_member_name">
		<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" alt="Lien vers la bio"><h3 class="entry-title"><?php the_title(); ?></h3></a>
	</header>
	<span class="team_member_desc">
		<?php the_field( "type_enseignement" ) ?>
	</span><br />
	<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" alt="Lien vers la bio">Lire la bio &gt;&gt;</h3></a>
</article>
