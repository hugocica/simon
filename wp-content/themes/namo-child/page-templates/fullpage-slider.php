<?php
/**
 * Custom page template
 *
 * Template Name: FullPage Slider
 */
 
    get_header();
 
?>

<section id="revslider-container" class="full-height">
    <?php putRevSlider( "home" ) ?>
</section>

<?php
    get_footer();
?>