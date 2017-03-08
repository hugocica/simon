<?php global $wpalchemy_media_access; ?>

<div class="my_meta_control clearfix">
    <hr>
	<h4 style="color: #000;">Configurações de propriedades</h4>
	
    <?php while($mb->have_fields_and_multi('propriedades')): ?>
        <p class="col-md-12">
    	<?php $mb->the_group_open(); ?>
    
    		<p class="col-md-6">
	    		<?php $mb->the_field('nome'); ?>
        		<label for="<?php $mb->the_name(); ?>">Nome</label>
        		<input type="text" id="<?php $mb->the_name(); ?>" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
    		</p>
    
    		<div class="col-md-6">
    			<?php $mb->the_field('photo'); ?>
    			<label for="<?php $mb->the_name(); ?>">Foto</label>
    			<?php $wpalchemy_media_access->setGroupName('img-n'. $mb->get_the_index())->setInsertButtonLabel('Inserir'); ?>
    			<p>
    			<?php echo $wpalchemy_media_access->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
    			<?php echo $wpalchemy_media_access->getButton(); ?>
    			</p>
    		</div>
    		
    		<p class="col-md-6">
    			<?php $mb->the_field('description'); ?>
        		<label for="<?php $mb->the_name(); ?>">Descrição</label>
        		<input type="text" id="<?php $mb->the_name(); ?>" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
    		</p>
    		
    		<p class="col-md-6">
    			<?php $mb->the_field('location'); ?>
        		<label for="<?php $mb->the_name(); ?>">Localização</label>
        		<input type="text" id="<?php $mb->the_name(); ?>" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
    		</p>
    		
    		<p class="col-md-6">
    			<?php $mb->the_field('time'); ?>
        		<label for="<?php $mb->the_name(); ?>">Horário de funcionamento</label>
        		<input type="text" id="<?php $mb->the_name(); ?>" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
    		</p>
    		
    		<p class="col-md-6">
    			<?php $mb->the_field('link'); ?>
        		<label for="<?php $mb->the_name(); ?>">Site</label>
        		<input type="text" id="<?php $mb->the_name(); ?>" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
    		</p>
    
            <p class="col-md-12">
    		    <a href="#" class="dodelete button">Apagar membro</a>
    		</p>
    
    	<?php $mb->the_group_close(); ?>
    	</p>
    <?php endwhile; ?>

	<p class="col-md-12" style="margin-bottom:15px; padding-top:5px;">
		<a href="#" class="docopy-propriedades button">Adicionar membro</a>
		<a style="float:right; margin:0 10px;" href="#" class="dodelete-propriedades button">Remover Todos</a>
	</p>
</div>

<style>
	.my_meta_control .col-md-12 {
		float: left;
		width: 100%;
	}
	.my_meta_control .col-md-6 {
		float: left;
		width: 50%;
	}
	.my_meta_control input, 
	.my_meta_control select {
		width: 100%;
		height: 28px;
	}
</style>