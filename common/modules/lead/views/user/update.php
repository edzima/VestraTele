<?php

use common\modules\lead\models\LeadUser;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model LeadUser */

$this->title = Yii::t('lead', 'Update Lead User: {lead}', [
	'lead' => $model->lead->getName(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => $model->lead->getName(), 'url' => ['lead/view', 'id' => $model->lead_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Update');
?>
<div class="lead-user-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
