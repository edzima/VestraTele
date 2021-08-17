<?php

use backend\widgets\IssueColumn;
use common\models\provision\ProvisionReportSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\provision\ProvisionUserReportWidget;
use yii\web\View;
use yii\web\YiiAsset;

/* @var $this View */
/* @var $searchModel ProvisionReportSearch */

$this->title = Yii::t('provision',
	'Report: {user} ({fromDate} - {toDate})', [
		'user' => $searchModel->toUser,
		'fromDate' => Yii::$app->formatter->asDate($searchModel->dateFrom),
		'toDate' => Yii::$app->formatter->asDate($searchModel->dateTo),
	]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions'), 'url' => ['provision/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Reports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>

<?= ProvisionUserReportWidget::widget([
	'issueColumn' => [
		'class' => IssueColumn::class,
		'viewBaseUrl' => null,
	],
	'actionColumn' => [
		'class' => ActionColumn::class,
		'template' => '{delete}',
	],
	'model' => $searchModel->summary(),
]) ?>



