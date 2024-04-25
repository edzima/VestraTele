<?php

use common\modules\lead\models\forms\LeadCostForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var LeadCostForm $model */

$this->title = Yii::t('lead', 'Update Lead Cost: {name}', [
	'name' => $model->getModel()->getName(),
]);
$this->params['breadcrumbs'][] = ['url' => ['lead/index'], 'label' => Yii::t('lead', 'Leads')];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Costs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->getName(), 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Update');
?>
<div class="lead-cost-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
