<?php

use common\modules\lead\models\forms\ReportForm;
use common\modules\lead\widgets\LeadReportWidget;
use common\modules\lead\widgets\SameContactsListWidget;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model ReportForm */

$this->title = Yii::t('lead', 'Create Report for Lead: {name}', ['name' => $model->getLead()->getName()]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getLead()->getName(), 'url' => ['lead/view', 'id' => $model->getLead()->getId()]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="lead-report-report">
	<h1><?= Html::encode($this->title) ?></h1>

	<div class="row">
		<div class="col-md-6">
			<?= $this->render('_lead', [
				'model' => $model->getLead(),
			]) ?>

			<?= $this->render('_form', [
				'model' => $model,
			]) ?>
		</div>

		<div class="col-md-6">
			<?php if (!empty($model->getLead()->reports)): ?>
				<h4><?= Yii::t('lead', 'Reports') ?></h4>
				<?php foreach ($model->getLead()->reports as $report): ?>

					<?= LeadReportWidget::widget([
						'model' => $report,
						'withDelete' => false,
					]) ?>


				<?php endforeach; ?>
			<?php endif; ?>

			<?= SameContactsListWidget::widget([
				'model' => $model->getLead(),
				'withType' => true,
			]) ?>
		</div>
	</div>

</div>







