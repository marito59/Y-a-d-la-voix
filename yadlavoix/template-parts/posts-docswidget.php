<li class="doc_item">
    <?php
        $file = get_field('document_pdf');
    ?>
    <a href="<?php echo $file['url']; ?>"  title="<?php the_title(); ?>" alt="lien vers <?php the_title(); ?>" class="doc_item_link" target="_blank"><?php the_title(); ?></a>
</li>