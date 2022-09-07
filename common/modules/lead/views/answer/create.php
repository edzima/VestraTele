<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\lead\models\LeadAnswer */

$this->title = Yii::t('lead', 'Create Lead Answer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Answers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-answer-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
