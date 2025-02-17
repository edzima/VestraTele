<?php

use common\modules\lead\models\forms\ReportForm;
use common\modules\lead\widgets\LeadStatusDetailsWidget;
use common\modules\lead\widgets\SameContactsGridView;
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
		<div class="col-md-9 col-lg-6">
			<?= $this->render('_lead', [
				'model' => $model->getLead(),
			]) ?>

			<?= $this->render('_form', [
				'model' => $model,
			]) ?>
		</div>

		<div class="col-md-6">

			<?= LeadStatusDetailsWidget::widget([]) ?>

			<?= SameContactsGridView::widget([
				'model' => $model->getLead(),
				'withType' => true,
			]) ?>

		</div>
	</div>

</div>







