<div class="sp sp_basic <?php echo $pagi->lang(); ?>">
	
	<div class="<?php echo $pagi->skin().' '.$pagi->color() ?>">
	
<?php 	if ($current_first_page) : ?>
	
			<a href="<?php echo $pagi->url($current_first_page) ?>">
				<?php echo $pagi->t('First') ?>
			</a>

<?php 	else : ?>

			<span class="disabled">
				<?php echo $pagi->t('First') ?>
			</span>

<?php	endif;

		if ($current_prev_page): ?>

			<a href="<?php echo $pagi->url($current_prev_page) ?>">
				<?php echo $pagi->t('Previous') ?>
			</a>	
		
<?php 		else: ?>

			<span class="disabled">
				<?php echo $pagi->t('Previous') ?>
			</span>
			
<?php	endif;
		
		// Loop through all pages and display ...
		for ($i = 1; $i <= $current_total_pages; $i++) :
		
			if ($i == $current_page) : ?>
			
				<span class="current"><?php echo $i ?></span>
			
<?php		else : ?>
	
				<a href="<?php echo $pagi->url($i) ?>"><?php echo $i ?></a>			
			
<?php		endif;
			
		endfor;
		
		if ($current_next_page) : ?>
	
			<a href="<?php echo $pagi->url($current_next_page); ?>">
				<?php echo $pagi->t('Next') ?>
			</a>
		
<?php 	else : ?>

			<span class="disabled">
				<?php echo $pagi->t('Next') ?>
			</span>
			
<?php	endif;

		if ($current_last_page) : ?>
	
			<a href="<?php echo $pagi->url($current_last_page); ?>">
				<?php echo $pagi->t('Last') ?>
			</a>
		
<?php 	else :  ?>

			<span class="disabled">
				<?php echo $pagi->t('Last') ?>
			</span>
			
<?php	endif;	?>
	
	</div>
	
</div>