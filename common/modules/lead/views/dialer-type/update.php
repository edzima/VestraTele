<?php

use common\modules\lead\models\LeadDialerType;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model LeadDialerType */

$this->title = Yii::t('lead', 'Update Type: {name}', [
	'name' => $model->name,
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Dialers'), 'url' => ['dialer/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Update');
?>
<div class="lead-dialer-type-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
