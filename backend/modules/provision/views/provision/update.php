<?php

use backend\helpers\Breadcrumbs;
use backend\modules\provision\models\ProvisionUpdateForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model ProvisionUpdateForm */

$this->title = Yii::t('provision', 'Update provision: #{id}', ['id' => $model->getModel()->id]);
$this->params['breadcrumbs'] = array_merge(
	Breadcrumbs::issue($model->getModel()->pay->calculation),
	Breadcrumbs::settlement($model->getModel()->pay->calculation)
);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provision-update">


	<?= DetailView::widget([
		'model' => $model->getModel(),
		'attributes' => [
			'toUser',
			'fromUserString',
			[
				'attribute' => 'value',
				'format' => 'currency',
			],
			[
				'attribute' => 'pay.valueWithVAT',
				'format' => 'currency',
			],
			[
				'attribute' => 'pay.valueWithoutVAT',
				'format' => 'currency',
			],
		],
	]) ?>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
