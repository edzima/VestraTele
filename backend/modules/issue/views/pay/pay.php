<?php

use common\models\issue\IssuePay;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssuePay */
/* @var $isPayed bool */
$this->title = $isPayed ? 'Edytuj wpłate: ' : 'Opłać wpłate: ' . Yii::$app->formatter->asCurrency($model->value);
$this->params['breadcrumbs'][] = ['label' => 'Wpłaty', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Opłać';
?>
<div class="issue-note-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
