<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\lead\models\LeadIssue */

$this->title = Yii::t('lead', 'Create Lead Issue');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Issues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-issue-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
