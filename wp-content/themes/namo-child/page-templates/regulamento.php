<?php
/**
 * Custom page template
 *
 * Template Name: Regulamento
 */
 
    get_header();
 
?>

<section id="regulamento-wrapper" class="full-height">
    
    <?php
        
        $image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
		$image_url = $image_url[0];
				
		$form = get_post_meta( get_the_ID(), '_form_metabox', true );
    ?>

    <div class="entry-content be-wrap">
        
        <?php
        
            if (have_posts()) :
                while (have_posts()) : the_post();
                    echo get_the_content();
                endwhile;
            endif;
			
        ?>
        
    </div>
    
</section>

<?php
    get_footer();
?>