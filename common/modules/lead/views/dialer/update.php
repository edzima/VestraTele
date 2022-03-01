<?php

use common\modules\lead\models\forms\LeadDialerForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model LeadDialerForm */

$this->title = Yii::t('lead', 'Update Lead Dialer: {name}', [
	'name' => $model->getModel()->id,
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->lead->getName(), 'url' => ['lead/view', 'id' => $model->getModel()->lead_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Dialers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->id, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Update');
?>
<div class="lead-dialer-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
