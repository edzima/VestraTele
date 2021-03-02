<?php

use backend\helpers\Breadcrumbs;
use backend\helpers\Url;
use backend\modules\provision\models\ProvisionForm;
use common\widgets\settlement\SettlementDetailView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ProvisionForm */

$this->title = Yii::t('provision', 'Update provision: #{id}', ['id' => $model->getId()]);
$this->params['breadcrumbs'] = array_merge(
	Breadcrumbs::issue($model->getModel()->pay->calculation),
	Breadcrumbs::settlement($model->getModel()->pay->calculation)
);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provision-update">


	<?= \yii\widgets\DetailView::widget([
		'model' => $model->getModel(),
		'attributes' => [
			'toUser',
			'fromUserString',
			[
				'attribute' => 'value',
				'format' => 'currency',
			],
			[
				'attribute' => 'pay.value',
				'format' => 'currency',
			],
		],
	]) ?>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
