<div class="sp sp_digg <?php echo $pagi->lang(); ?>">
	
	<div class="<?php echo $pagi->skin().' '.$pagi->color() ?>">

	<?php if ($current_prev_page) : ?>
		
		<a class="prev" href="<?php echo $pagi->url($current_prev_page) ?>">
			&laquo;&nbsp; <?php echo $pagi->t('Previous') ?>
		</a>
		
	<?php else : ?>
	
		<span class="disabled prev">
			&laquo;&nbsp; <?php echo $pagi->t('Previous') ?>
		</span>
		
	<?php endif;

		if ($current_total_pages < 10) :

			for ($i = 1; $i <= $current_total_pages; $i++) :
		
				if ($i == $current_page): ?>
			
				<span class="current"><?php echo $i ?></span>
				
			<?php else : ?>
			
				<a href="<?php echo $pagi->url($i) ?>"><?php echo $i ?></a>
				
			<?php endif;
			
			endfor;

		elseif ($current_page < 6) :

			for ($i = 1; $i <= 6; $i++) :
		
				if ($i == $current_page) : ?>
			
				<span class="current"><?php echo $i ?></span>
				
			<?php else :?>
		
				<a href="<?php echo $pagi->url($i) ?>"><?php echo $i ?></a>
				
			<?php endif;
			
			endfor; ?>

		<span class="disabled">&hellip;</span>		
		<a href="<?php echo $pagi->url($current_total_pages - 1) ?>">
			<?php echo $current_total_pages - 1 ?>
		</a>
		<a href="<?php echo $pagi->url($current_total_pages) ?>">
			<?php echo $current_total_pages ?>
		</a>

	<?php elseif ($current_page > $current_total_pages - 6) : ?>
		
		<a href="<?php echo $pagi->url(1) ?>">1</a>
		<a href="<?php echo $pagi->url(2) ?>">2</a>		
		<span class="disabled">&hellip;</span>

		<?php for ($i = $current_total_pages - 6; $i <= $current_total_pages; $i++) :
		
				if ($i == $current_page) : ?>
			
				<span class="current"><?php echo $i ?></span>
				
			<?php else : ?>
		
				<a href="<?php echo $pagi->url($i) ?>"><?php echo $i ?></a>
				
			<?php endif;
			
			endfor;

		else : ?>
		
		<a href="<?php echo $pagi->url(1) ?>">1</a>
		<a href="<?php echo $pagi->url(2) ?>">2</a>
		<span class="disabled">&hellip;</span>

		<?php for ($i = $current_page - 2; $i <= $current_page + 3; $i++) :
		
				if ($i == $current_page) : ?>
			
				<span class="current"><?php echo $i ?></span>
				
			<?php else : ?>
			
				<a href="<?php echo $pagi->url($i) ?>"><?php echo $i ?></a>
				
			<?php endif;
			
			endfor; ?>

		<span class="disabled">&hellip;</span>		
		<a href="<?php echo $pagi->url($current_total_pages - 1) ?>">
			<?php echo $current_total_pages - 1 ?>
		</a>
		<a href="<?php echo $pagi->url($current_total_pages) ?>">
			<?php echo $current_total_pages ?>
		</a>

	<?php endif;

		if ($current_next_page) : ?>
	
		<a href="<?php echo $pagi->url($current_next_page) ?>" class="next">
			<?php echo $pagi->t('Next') ?> &nbsp;&raquo;
		</a>
		
	<?php else : ?>
	
		<span class="disabled next">
			<?php echo $pagi->t('Next') ?> &nbsp;&raquo;
		</span>
		
	<?php endif; ?>	
	
	</div>
	
</div>