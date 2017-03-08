<div class="my_meta_control">
	<p style="display: inline-block;width: 80%;">
		<?php $mb->the_field('form_id'); ?>
		<label for="<?php $metabox->the_name(); ?>">Formulário:</label>
        <?php
			global $wpdb;
			$result = $wpdb->get_results( 'SELECT id, name FROM wp_frm_forms WHERE is_template=0 AND status="published"' );
		?>
		<select name="<?php $mb->the_name(); ?>">
			<option value="">Selecione uma formulário...</option>
			<?php
				foreach ( $result as $row ) {
					$selected = ($mb->get_the_value() == $row->id)?'selected="selected"':'';
					echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
				}
			?>
		</select>
	</p>
</div> 