<?php
	$post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'] ;
	$template_file = str_replace( 'page-templates/', '', get_post_meta( $post_id, '_wp_page_template', true ) );
	
	if ( $template_file == 'descontos.php' ) {
		$form_config = new WPAlchemy_MetaBox(
						array(
							'id' => '_form_metabox',
							'title' => 'Formulário',
							'types' => array('page'),
							'context' => 'normal',
							'priority' => 'high',
							'template' => get_stylesheet_directory() . '/metaboxes/form-meta.php'
						)
					);
		$voucher_config = new WPAlchemy_MetaBox(
						array(
							'id' => '_voucher_metabox',
							'title' => 'Link do Voucher',
							'types' => array('page'),
							'context' => 'normal',
							'priority' => 'high',
							'template' => get_stylesheet_directory() . '/metaboxes/voucher-meta.php'
						)
					);
	}
	
	$title_config = new WPAlchemy_MetaBox(
						array(
							'id' => '_pagetitle_metabox',
							'title' => 'Título da Página',
							'types' => array('page'),
							'context' => 'side',
							'priority' => 'high',
							'template' => get_stylesheet_directory() . '/metaboxes/title-meta.php'
						)
					);
					
	$propriedades_config = new WPAlchemy_MetaBox(
						array(
							'id' => '_propriedades_metabox',
							'title' => 'Propriedades',
							'types' => array('post'),
							'context' => 'normal',
							'priority' => 'high',
							'template' => get_stylesheet_directory() . '/metaboxes/propriedades-meta.php'
						)
					);
?>