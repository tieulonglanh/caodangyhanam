<div class="paging sp sp_basic <?php echo $pagi->lang(); ?>">
    <ul class="<?php echo $pagi->skin().' '.$pagi->color() ?>">
        <?php 	if ($current_first_page): ?>
        <li><a href="<?php echo $pagi->url($current_first_page) ?>" class="first"><?php echo $pagi->t('First') ?></a></li>
        <?php 	else : ?>
        <li><a href="<?php echo $pagi->url($current_first_page) ?>" class="first disabled"><?php echo $pagi->t('First') ?></a></li>
        <?php	endif; ?>
        <?php   if ($current_prev_page): ?>
        <li><a href="<?php echo $pagi->url($current_prev_page) ?>" class="back"><?php echo $pagi->t('Previous') ?></a></li>
        <?php   else: ?>
        <li><a href="<?php echo $pagi->url($current_prev_page) ?>" class="back disabled"><?php echo $pagi->t('Previous') ?></a></li>
        <?php	endif; ?>
        <?php
        // Loop through all pages and display ...
        for ($i = 1; $i <= $current_total_pages; $i++):
                if ($i == $current_page): ?>
                    <li class="current"><a href="javascript:void();"><?php echo $i ?></a></li>
        <?php   else: ?>
                <li><a href="<?php echo $pagi->url($i) ?>"><?php echo $i ?></a></li>
        <?php   endif;
        endfor; ?>
        <?php   if ($current_next_page): ?>
            <li><a href="<?php echo $pagi->url($current_next_page); ?>" class="next"><?php echo $pagi->t('Next') ?></a></li>
        <?php 	else: ?>
            <li><a href="<?php echo $pagi->url($current_next_page); ?>" class="next disabled"><?php echo $pagi->t('Next') ?></a></li>
        <?php	endif; ?>

        <?php   if ($current_last_page) : ?>
            <li><a href="<?php echo $pagi->url($current_last_page); ?>" class="last"><?php echo $pagi->t('Last') ?></a></li>
        <?php 	else :  ?>
            <li><a href="<?php echo $pagi->url($current_last_page); ?>" class="last disabled"><?php echo $pagi->t('Last') ?></a></li>
        <?php	endif;	?>
    </ul>
    <div style="clear: both"></div>
</div>