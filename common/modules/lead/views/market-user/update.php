<?php

use common\modules\lead\models\LeadMarketUser;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model LeadMarketUser */

$this->title = Yii::t('lead', 'Update Lead: {lead} Market User: {user}', [
	'lead' => $model->market->lead->getName(),
	'user' => $model->user->getFullName(),
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Market'), 'url' => ['market/index']];
$this->params['breadcrumbs'][] = ['label' => $model->market_id, 'url' => ['market/view', 'id' => $model->market_id]];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Update');
?>
<div class="lead-market-user-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
