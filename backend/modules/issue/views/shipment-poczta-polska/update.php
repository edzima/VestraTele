<?php

use backend\helpers\Breadcrumbs;

/** @var yii\web\View $this */
/** @var common\models\issue\IssueShipmentPocztaPolska $model */

$this->title = Yii::t('issue', 'Update Issue Shipment Poczta Polska: #{number}', [
	'number' => $model->shipment_number,
]);
$this->params['breadcrumbs'] = Breadcrumbs::issue($model->issue);
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issue Shipment Poczta Polska'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->shipment_number, 'url' => ['view', 'issue_id' => $model->issue_id, 'shipment_number' => $model->shipment_number]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="issue-shipment-poczta-polska-update">

	<?= $this->render('_form', [
		'model' => $model,
		'withIssueField' => true,
	]) ?>

</div>
