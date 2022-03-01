<?php

use common\modules\lead\models\forms\LeadDialerForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model LeadDialerForm */

if ($model->scenario === LeadDialerForm::SCENARIO_MULTIPLE) {
	$this->title = Yii::t('lead', 'Assign {count} Leads to Dialer', [
		'count' => count($model->leadId),
	]);
} else {
	$this->title = Yii::t('lead', 'Assign Lead to Dialer');
}

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Dialers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-dialer-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
