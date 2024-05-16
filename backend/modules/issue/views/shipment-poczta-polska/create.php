<?php

use backend\helpers\Breadcrumbs;
use common\models\issue\IssueInterface;
use common\models\issue\IssueShipmentPocztaPolska;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var IssueShipmentPocztaPolska $model */
/** @var IssueInterface|null $issue */

$this->title = Yii::t('issue', 'Create Issue Shipment Poczta Polska');
if ($issue !== null) {
	$this->title = Yii::t('issue', 'Create Shipment Poczta Polska to Issue: {issue}', [
		'issue' => $issue->getIssueName(),
	]);
	$this->params['breadcrumbs'] = Breadcrumbs::issue($issue);
} else {
	$this->params['breadcrumbs'][] = ['url' => ['issue/index'], 'label' => Yii::t('issue', 'Issues')];
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issue Shipment Poczta Polska'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-shipment-poczta-polska-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
		'withIssueField' => $issue === null,
	]) ?>

</div>
