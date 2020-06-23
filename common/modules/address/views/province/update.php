<?php

use common\models\address\Province;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model Province */

$this->title = Yii::t('address', 'Update province') . ": $model->name";
$this->params['breadcrumbs'][] = ['label' => Yii::t('address', 'Provinces'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id, 'wojewodztwo_id' => $model->wojewodztwo_id]];
$this->params['breadcrumbs'][] = 'Edycja';
?>
<div class="powiat-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
