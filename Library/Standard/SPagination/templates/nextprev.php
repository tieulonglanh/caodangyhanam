<div class="sp sp_nextprev <?php echo $pagi->lang(); ?>">
	
	<div class="<?php echo $pagi->skin().' '.$pagi->color() ?>">

<?php 	if (FALSE !== $current_first_page) : ?>
	
			<a href="<?php echo $pagi->url($current_first_page) ?>">
				<?php echo $pagi->t('First') ?>
			</a>

<?php 	else : ?>

			<span class="disabled">
				<?php echo $pagi->t('First') ?>
			</span>

<?php	endif;

		if (FALSE !== $current_prev_page): ?>

			<a href="<?php echo $pagi->url($current_prev_page) ?>">
				&#171; <?php echo $pagi->t('Previous') ?>
			</a>	
		
<?php 		else: ?>

			<span class="disabled">
				&#171; <?php echo $pagi->t('Previous') ?>
			</span>

<?php	endif;

		if (FALSE !== $current_next_page) : ?>
	
		<a href="<?php echo $pagi->url($current_next_page); ?>">
			<?php echo $pagi->t('Next') ?> &#187;
		</a>
		
<?php 	else : ?>

		<span class="disabled">
			<?php echo $pagi->t('Next') ?> &#187;
		</span>
		
<?php 	endif;

		// Show link to last page if not on it already?
		if (FALSE !== $current_last_page) : ?>
	
			<a href="<?php echo $pagi->url($current_last_page); ?>">
				<?php echo $pagi->t('Last') ?>
		 	</a>
		
<?php 	else : ?>

			<span class="disabled">
				<?php echo $pagi->t('Last') ?>
		 	</span>
		
<?php 	endif; ?>

	</div>
	
</div>