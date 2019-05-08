<?php

use backend\modules\issue\models\PayForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model PayForm */

$this->title = 'Edycja wpłaty: ' . $model->pay->issue;
$this->params['breadcrumbs'][] = ['label' => 'Wpłaty', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Edycja';
?>
<div class="issue-pay-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
