<?php

use common\models\PotentialClient;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model PotentialClient */

$this->title = Yii::t('common',
	'Update Potential Client: {name}', [
		'name' => $model->name,
	]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Potential Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="potential-client-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
