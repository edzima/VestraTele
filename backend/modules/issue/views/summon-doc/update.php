<?php

use backend\modules\issue\models\SummonDocForm;

/* @var $this yii\web\View */
/* @var $model SummonDocForm */

$this->title = Yii::t('backend', 'Update Summon Doc: {name}', [
	'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['/issue/summon/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Summon Docs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="summon-doc-update">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
