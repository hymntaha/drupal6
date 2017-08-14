<div class="field field-name-field-class-schedule field-label-inline clearfix">
	<div class="field-label">Schedule:&nbsp;</div>
	<div class="field-items">
		<?php if(isset($dates['non_recurring'])): ?>
			<div class="field-item non-recurring">
				<?=render($dates['non_recurring']);?>
			</div>
		<?php endif;?>
		<?php if(isset($dates['recurring'])): ?>
			<div class="field-item recurring">
				<p class="recurring-date-range"><?=render($dates['recurring']['date_range']);?></p>
				<p class="recurring-times"><?=render($dates['recurring']['times']);?></p>
			</div>
		<?php endif;?>
	</div>
</div>