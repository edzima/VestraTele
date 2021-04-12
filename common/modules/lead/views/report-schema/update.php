<?php

use common\modules\lead\models\forms\LeadReportSchemaForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model LeadReportSchemaForm */

$this->title = Yii::t('lead', 'Update Lead Report Schema: {name}', [
	'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Report Schemas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Update');
?>
<div class="lead-report-schema-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
