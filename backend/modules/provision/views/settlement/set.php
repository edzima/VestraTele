<?php

use backend\helpers\Breadcrumbs;
use backend\modules\provision\models\SettlementProvisionsForm;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model SettlementProvisionsForm */

$this->title = Yii::t('backend', 'Set provisions for settlement: {type}', ['type' => $model->getModel()->getTypeName()]);
$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssue());
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['/settlement/calculation/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getIssue()->longId, 'url' => ['/settlement/calculation/issue', 'id' => $model->getIssue()->id]];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->getTypeName(), 'url' => ['/settlement/calculation/view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Set provisions for settlement');

?>
<div class="provision-settlement-set">

	<?= DetailView::widget([
		'model' => $model->getModel(),
		'attributes' => [
			'providerName',
			'value:currency',
			[
				'attribute' => 'valueToPay',
				'format' => 'currency',
				'visible' => !$model->getModel()->isPayed(),
			],
			[
				'attribute' => 'payment_at',
				'format' => 'date',
				'visible' => $model->getModel()->isPayed(),
			],
			[
				'attribute' => 'details',
				'format' => 'ntext',
				'visible' => !empty($model->getModel()->details),
			],
		],
	]) ?>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
