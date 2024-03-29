<?php

use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\forms\LeadMarketForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $lead ActiveLead */
/* @var $model LeadMarketForm */

$this->title = Yii::t('lead', 'Add Lead: {name} to Market', [
	'name' => $lead->getName(),
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => $lead->getName(), 'url' => ['lead/view', 'id' => $lead->getId()]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Markets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-market-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
