<?php

use common\modules\lead\models\forms\LeadMarketForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model LeadMarketForm */

$this->title = Yii::t('lead', 'Update Lead Market: {name}', [
	'name' => $model->getModel()->lead->getName(),
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->lead->getName(), 'url' => ['lead/view', 'id' => $model->getModel()->lead_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Markets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->id, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Update');
?>
<div class="lead-market-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
