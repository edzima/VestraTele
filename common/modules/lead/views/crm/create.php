<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\lead\models\LeadCrm */

$this->title = Yii::t('lead', 'Create Lead CRM');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead CRMs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-crm-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
