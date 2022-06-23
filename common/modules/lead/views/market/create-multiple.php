<?php

use common\helpers\Html;
use common\modules\lead\models\forms\LeadMarketMultipleForm;

/* @var $this yii\web\View */
/* @var $model LeadMarketMultipleForm */

$this->title = Yii::t('lead', 'Move Leads to Market ({count})', [
	'count' => count($model->leadsIds),
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Markets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-market-create-multiple">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
