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
		$voucher = get_post_meta( get_the_ID(), '_voucher_metabox', true );
    ?>

    <div class="entry-thumb half-section full-height" style="background-image: url(<?php echo $image_url; ?>);">
        
    </div>
    
    <div class="form-descontos-container half-section full-height">
        <?php echo do_shortcode('[formidable id='. $form['form_id'] .']'); ?>
    </div>
    
</section>

<?php
    if ( true ) {
        // unset($_SESSION['frm_success']);
        //var_dump(get_the_ID());
        //$voucher[''];
        // rwmb_meta( $voucher, $args = array(), $post_id = null );
?>

    <section id="voucher" style="display:none;">
        <div class="popup">
            <button id="button-close-voucher"><b>X</b></button>
            <h2 class ="popuptitle">Obrigado pela participação!</h2>
            <span class="popuptext"><p>Agora é só acessar o link abaixo para acessar seu voucher de desconto <b>e boas compras!</b></p></span>
            <button id="buttonvoucher"><b>Acessar voucher</b></button>
        </div>
    </section>

<?php
    }
?>

<script>
    jQuery(document).ready(function($) {
        $('.custom-select select').customSelect();
    
        jQuery('#button-close-voucher').click(function() {
	        jQuery('#voucher').fadeOut(400);
        });
        
        jQuery('#buttonvoucher').click(function() {
	        jQuery('#buttonvoucher').fadeTo(400, 0, function () {
	            var textVar = '<?php echo $voucher['cupom']; ?>';
	            console.log(textVar);
		        $(this).delay(400);
		        $(this).text(textVar);
		        $(this).fadeTo(400, 1);
	        });
        });
        
        // $('#btnDialog').click(function () {
        //     $('#voucher').fadeIn(400);
        // });
});
    
</script>

<?php
    get_footer();
?>