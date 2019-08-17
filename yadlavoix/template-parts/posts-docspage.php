<?php
    $file = get_field('document_pdf');
?>
<li class="doc_item <?php join( ' ', get_post_class() ); ?>" id="attachment-<?php echo $file["ID"] ?>">
<img class="picto" src="<?php echo get_bloginfo('stylesheet_directory') . '/images/docu.png'; ?>" alt="Document" />
<h2><?php echo $file["title"]; ?></h2>
<p><?php echo $file["caption"]; ?></p>
<a href="<?php echo $file["url"]; ?>" target="_blank"><img class="lire" src="<?php echo get_bloginfo('stylesheet_directory') . '/images/lire.png';?>" alt="lire" /></a>
</li>