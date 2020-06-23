<?php

use common\models\address\Province;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model Province */

$this->title = Yii::t('address', 'Create province');
$this->params['breadcrumbs'][] = ['label' => Yii::t('address', 'Provinces'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="powiat-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
