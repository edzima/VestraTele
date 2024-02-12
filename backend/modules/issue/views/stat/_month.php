<?php

use backend\modules\issue\models\IssueStats;
use common\helpers\Url;

/* @var $model IssueStats */
/* @var $comparativeModel IssueStats */

$baseCount = $model->getCountForMonth();

$comparativeCount = $comparativeModel ? $comparativeModel->getCountForMonth() : 0;
$percent = $comparativeCount > 0
	? ($baseCount / $comparativeCount)
	: 0;

$color = 'blue';
if ($percent) {
	if ($percent >= 1) {
		$color = 'green';
	} else {
		if ($percent > 0.8) {
			$color = 'yellow';
		} else {
			$color = 'red';
		}
	}
}

$progressText = '';
if ($percent !== 0 && $percent !== 1) {
	if ($percent > 1) {
		$progressText = Yii::t('common', 'Increase by {percent}', [
			'percent' => Yii::$app->formatter->asPercent($percent - 1),
		]);
	} else {
		$progressText = Yii::t('common', 'Decrease by {percent}', [
			'percent' => Yii::$app->formatter->asPercent(1 - $percent),
		]);
	}
}

if ($percent > 1) {
	$progressValue = ($percent - 1) * 100;
}
$diff = $baseCount - $comparativeCount;
$diffInfo = Yii::t('common', 'Without changes');
if ($diff > 0) {
	$diffInfo = Yii::t('common', '{diff} more', [
		'diff' => $diff,
	]);
} else {
	$diffInfo = Yii::t('common', '{diff} less', [
		'diff' => $diff * -1,
	]);
}
?>
<?php if ($baseCount > 0 || $comparativeCount > 0): ?>

	<div class="month-box">
		<a
				class="info-box bg-<?= $color ?>"
				data-pjax="1"
				href="<?= Url::to(['details', 'year' => $model->year, 'month' => $model->month, Url::PARAM_ISSUE_PARENT_TYPE => $model->issueMainTypeId]) ?>"
		>

			<span class="info-box-icon"><?= $baseCount ?></span>
			<div class="info-box-content">
				<span class="info-box-text"><?= $model->getMonthName() ?></span>

				<?php if ($percent): ?>
					<span class="info-box-number"><?= $diffInfo ?></span>
					<div class="progress">
						<div class="progress-bar" style="width: <?= $percent <= 1 ? $percent * 100 : ($percent - 1) * 100 ?>%"></div>
					</div>
					<span class="progress-description">
	                <?= $progressText ?>
				</span>
				<?php endif ?>

			</div>
		</a>

	</div>

<?php endif; ?>
