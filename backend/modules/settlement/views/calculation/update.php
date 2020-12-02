<?php

use backend\helpers\Breadcrumbs;
use backend\modules\settlement\models\CalculationForm;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model CalculationForm */

$this->title = Yii::t('backend', 'Update settlement: {type}', ['type' => $model->getModel()->getTypeName()]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssue());
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getIssue()->longId, 'url' => ['issue', 'id' => $model->getIssue()->id]];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->getTypeName(), 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="issue-pay-calculation-update">

	<h1><?= Html::encode($this->title) ?></h1>
	<div class="row">
		<div class="col-md-5">
			<?= DetailView::widget([
				'model' => $model->getIssue(),
				'attributes' => [
					'customer.fullName:text:' . $model->getIssue()->getAttributeLabel('customer'),
					'type',
					'stage',
				],
			]) ?>

		</div>
	</div>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
