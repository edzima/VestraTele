<?php

use backend\helpers\Html;
use backend\helpers\Url;
use backend\modules\issue\models\HostIssueStats;
use backend\modules\issue\models\IssueStats;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model IssueStats */

$this->title = Yii::t('backend', 'Stats');

$this->params['issueParentTypeNav'] = [
	'route' => ['/issue/stat/index', Html::getInputName($model, 'type_id') => $model->issueMainTypeId],
];

$yearsModel = $model->getYearsModels();

?>

<div class="issue-stats-index">

	<h2>Zarejestrowane Sprawy</h2>

	<p>
		<?= Html::a(Yii::t('common', 'Charts'),
			['chart', Url::PARAM_ISSUE_PARENT_TYPE => $model->issueMainTypeId], [
				'class' => 'btn btn-success',
			])
		?>
		<?= HostIssueStats::hasHosts()
			? Html::a(Yii::t('common', 'CRM'),
				['hosts'], [
					'class' => 'btn btn-info',
				])
			: ''
		?>
	</p>


	<?php foreach ($yearsModel as $year => $yearModel): ?>

		<div class="row">
			<?php Pjax::begin([
				'enablePushState' => false,
				'clientOptions' => [
					'container' => '#pjax-wrapper-' . $year,
				],
			]) ?>
			<div class="col-md-2">
				<a class="info-box bg-blue" href="<?= Url::to(['year', 'year' => $year, Url::PARAM_ISSUE_PARENT_TYPE => $model->issueMainTypeId]) ?>">
					<span class="info-box-icon"><i class="fa fa-suitcase"></i></span>
					<div class="info-box-content">
						<span class="info-box-text"><?= $year ?></span>
						<span class="info-box-number"><?= $yearModel->getCountForYear() ?></span>
						<?php if ($yearModel->getAllCount() !== $yearModel->getCountForYear()): ?>
							<div class="progress">
								<div class="progress-bar" style="width: <?= $yearModel->getCountForYear() / $yearModel->getAllCount() * 100 ?>%"></div>
							</div>
							<span class="progress-description">
	                    <?= $yearModel->getCountForYear() . '/' . $yearModel->getAllCount() ?></span>
						<?php endif; ?>
					</div>
				</a>
			</div>

			<div class="issue-create-count-grid col-md-10">

				<?php
				$monthsModels = $yearModel->getMonthModels();
				foreach ($monthsModels as $month => $monthModel) {
					if ($month > 1) {
						$comparativeModel = $monthsModels[$month - 1] ?? null;
					} else {
						$previousYear = $yearsModel[$year - 1] ?? null;
						if ($previousYear) {
							$comparativeModel = $previousYear->getMonthModels()[12] ?? null;
						}
					}
					echo $this->render('_month', [
						'model' => $monthModel,
						'comparativeModel' => $comparativeModel,
					]);
				}
				?>

			</div>
			<?php Pjax::end() ?>
		</div>
		<br>
		<div class="col-md-12" id="pjax-wrapper-<?= $year ?>"></div>
		<br>
	<?php endforeach; ?>


</div>

