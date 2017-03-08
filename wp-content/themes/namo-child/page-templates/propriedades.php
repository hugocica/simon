<?php
/**
 * Custom page template
 *
 * Template Name: Propriedades
 */
 
    get_header();
 
?>

<section id="propriedades-slider-wrapper" class="full-height">
    
    <?php
        $currentControl = true;
				
		$propriedades = get_category_by_slug('propriedades');
		$args = array (
        	'pagination'        => false,
        	'posts_per_page'    => '-1',
        	'cat'               => $propriedades->term_id,
        	'orderby '          => 'ID',
        	'order'             => 'ASC'
        );		
	    
	    $query = new WP_Query( $args );
    ?>

    <?php if ( $query->have_posts() ) : ?>
    
    	<div id="propriedades-featured" class="royalSlider rsDefault full-height full-section">
    
    	<?php 
    	    while ( $query->have_posts() ) : 
    			$query->the_post(); 
    
        		//Set image sizes
        		$ht_thumbnail_id = get_post_thumbnail_id( get_the_ID() );		
        		$ht_slider_image_320 = wp_get_attachment_image_src( $ht_thumbnail_id, 'width=320&height=0&crop=resize-crop' );
        		$ht_slider_image_480 = wp_get_attachment_image_src( $ht_thumbnail_id, 'width=480&height=0&crop=resize-crop' );
        		$ht_slider_image_600 = wp_get_attachment_image_src( $ht_thumbnail_id, 'width=600&height=0&crop=resize-crop' );
        		$ht_slider_image_920 = wp_get_attachment_image_src( $ht_thumbnail_id, 'width=920&height=0&crop=resize-crop' );
        		$ht_slider_image_1200 = wp_get_attachment_image_src( $ht_thumbnail_id, 'width=1200&height=0&crop=resize-crop' ); 
                
                $image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
                $image_url = $image_url[0];
    	?>
    
    		<div class="rsContent full-height propriedade" data-pid="<?php the_ID(); ?>" style="background-image: url(<?php echo $image_url; ?>)">
    		    
    		    <div class="propriedade-btn">
    		        <h2 class="propriedade-title"><?php the_title(); ?></h2>
    		        <span class="">Clique e saiba mais</span>
    		    </div>
    		    
    	    </div>
    
    		<?php 
    		    endwhile; 
        		// Restore original Post Data
                wp_reset_postdata(); ?>
    	</div>
    	
    	<div class="carrousel-control">
    	    <div class="be-wrap">
                <?php while ( $query->have_posts() ) : $query->the_post();  ?>
                
                <div class="carrousel-item <?php echo ( $currentControl ) ? 'current' : '' ;?>" data-pid="<?php the_ID(); ?>">
                    <?php $currentControl = false; ?>
                    <span><?php the_title(); ?></span>
                </div>
                
                <?php endwhile; 
        		// Restore original Post Data
                wp_reset_postdata();
                ?>
            </div>
        </div>
    	
    <?php endif; ?>
    
    <!-- /#homepage-feature -->
    
    
</section>

<section class="propriedades-city-wrapper">
    <div class="close-content"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="512px" version="1.1" height="512px" viewBox="0 0 64 64" enable-background="new 0 0 64 64">  <g>    <path class="close-btn" fill="currentColor" d="M28.941,31.786L0.613,60.114c-0.787,0.787-0.787,2.062,0,2.849c0.393,0.394,0.909,0.59,1.424,0.59   c0.516,0,1.031-0.196,1.424-0.59l28.541-28.541l28.541,28.541c0.394,0.394,0.909,0.59,1.424,0.59c0.515,0,1.031-0.196,1.424-0.59   c0.787-0.787,0.787-2.062,0-2.849L35.064,31.786L63.41,3.438c0.787-0.787,0.787-2.062,0-2.849c-0.787-0.786-2.062-0.786-2.848,0   L32.003,29.15L3.441,0.59c-0.787-0.786-2.061-0.786-2.848,0c-0.787,0.787-0.787,2.062,0,2.849L28.941,31.786z"></path>  </g></svg></div>
</section>

<script>
    jQuery(document).ready(function($) {
        // slider
        var slider = $('#propriedades-featured').royalSlider({
      	    autoPlay: {
      		    enabled: true,
      		    delay: 5000
      	    },
            addActiveClass: true,
            arrowsNav: false,
            controlNavigation: 'none',
            autoScaleSlider: true,
            navigateByClick: false,
            loop: false,
        	autoScaleSliderWidth: '100vw',
        	autoScaleSliderHeight: '100vh', 
            fadeinLoadedSlide: true,
            globalCaption: false,
            keyboardNavEnabled: true,
            globalCaptionInside: false,
        	numImagesToPreload:50,
        	imageScaleMode:	'fill',
        	slidesSpacing:0,
    	}).data('royalSlider');
    	
        $('.carrousel-item').click(function() {
            var sliderId = $(this).data('pid');
            
            $('.carrousel-item').removeClass('current');
            $(this).addClass('current');
            slider.goTo( $('.propriedade[data-pid="'+ sliderId +'"]').parent().index() );
        });
        
        slider.ev.on('rsAfterSlideChange', function(event) {
            var sliderId = $('.rsActiveSlide').children('.propriedade').data('pid');
            
            $('.carrousel-item').removeClass('current');
            $('.carrousel-item[data-pid="'+ sliderId +'"]').addClass('current');
        });
        
        // ajax call
        $('.propriedade').click(function() {
            LoadPropriedades( $(this).data('pid') );
        });
    });
</script>
<?php
    get_footer();
?>