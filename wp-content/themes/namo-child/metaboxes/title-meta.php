<div class="my_meta_control">
	<p>
		<?php $mb->the_field('show_title'); ?>
		<label for="<?php $metabox->the_name(); ?>">Exibir título?</label> <br>
		<input type="radio" name="<?php $mb->the_name(); ?>" value="sim"<?php echo $mb->is_value('nao')?'':' checked="checked"'; ?>/><span style="margin-right:10px;">Sim</span> 
		<input type="radio" name="<?php $mb->the_name(); ?>" value="nao"<?php echo $mb->is_value('nao')?' checked="checked"':''; ?>/><span>Não</span> 
	</p>
</div> 