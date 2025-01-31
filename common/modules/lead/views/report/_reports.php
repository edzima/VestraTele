<?php

use common\modules\lead\models\Lead;
use common\modules\lead\widgets\LeadReportWidget;

/**
 * @var $model Lead
 */
$reports = $model->getReports()
	->with('owner')
	->with('answers')
	->all();
?>

<div class="reports-details">
	<?php if (!empty($reports)): ?>
		<?php foreach ($reports as $report): ?>
			<?= LeadReportWidget::widget([
				'model' => $report,
				'withDeleteButton' => false,
			]) ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
