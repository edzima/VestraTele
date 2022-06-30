<?php

use common\modules\lead\models\forms\LeadMarketAccessRequest;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model LeadMarketAccessRequest */

$this->title = Yii::t('lead', 'Access Request for Lead from Market');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getMarket()->lead->getName(), 'url' => ['lead/view', 'id' => $model->getMarket()->lead_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Market'), 'url' => ['market/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Market Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-market-user-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_access_form', [
		'model' => $model,
	]) ?>

</div>
