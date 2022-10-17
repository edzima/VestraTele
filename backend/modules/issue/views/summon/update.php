<?php

use backend\helpers\Breadcrumbs;
use backend\modules\issue\models\SummonForm;

/* @var $this yii\web\View */
/* @var $model SummonForm */

$this->title = Yii::t('common', 'Update Summon {type}', [
	'type' => $model->getModel()->typeName,
]);
$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getModel());
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->typeName, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="summon-update">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
