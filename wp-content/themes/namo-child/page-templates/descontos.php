<?php
/**
 * Custom page template
 *
 * Template Name: Descontos
 */
 
    get_header();
 
?>

<section id="descontos-wrapper" class="full-height">
    
    <?php
        
        $image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
		$image_url = $image_url[0];
				
		$form = get_post_meta( get_the_ID(), '_form_metabox', true );
    ?>

    <div class="entry-thumb half-section full-height" style="background-image: url(<?php echo $image_url; ?>);">
        
    </div>
    
    <div class="form-descontos-container half-section full-height">
        <?php echo do_shortcode('[formidable id='. $form['form_id'] .']'); ?>
    </div>
    
</section>

<?php
    get_footer();
?>