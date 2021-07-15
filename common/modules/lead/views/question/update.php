<?php

use common\modules\lead\models\forms\LeadQuestionForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model LeadQuestionForm */

$this->title = Yii::t('lead', 'Update Lead Question: {name}', [
	'name' => $model->name,
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Questions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Update');
?>
<div class="lead-question-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
