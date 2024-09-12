<?php

/* @var $nav string */
/* @var $donutUsersChart string */
/* @var $areaUsersStatusChart string */
/* @var $donutStatusesChart string */

?>

<div class="lead-user-status-chart-wrapper">
	<?= $nav ?>
	<br>
	<div class="row">
		<?php if (!empty($donutUsersChart)): ?>
			<div class="col-sm-12 col-md-7 col-lg-8">
				<?= $areaUsersStatusChart ?>
			</div>
			<div class="col-sm-12 col-md-5 col-lg-4">
				<?= $donutUsersChart ?>
			</div>
		<?php else: ?>
			<div class="col-sm-12">
				<?= $areaUsersStatusChart ?>
			</div>
		<?php endif; ?>

	</div>
</div>
