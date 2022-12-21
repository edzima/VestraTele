<?php

use backend\helpers\Html;
use backend\widgets\IssueColumn;
use common\models\provision\Provision;
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

<?= $this->render('_view_search', ['model' => $searchModel]) ?>


<?= ProvisionUserReportWidget::widget([
	'issueColumn' => [
		'class' => IssueColumn::class,
		'viewBaseUrl' => null,
	],
	'actionColumn' => [
		'class' => ActionColumn::class,
		'template' => '{update} {hide}',
		'buttons' => [
			'update' => static function (string $url, Provision $provision): string {
				return Html::a(Html::icon('pencil'), [
					'provision/update', 'id' => $provision->id,
				], [
					'target' => '_blank',
					'title' => Yii::t('common', 'Update'),
					'aria-label' => Yii::t('common', 'Update'),
				]);
			},
			'hide' => static function (string $url, Provision $provision): string {
				return Html::a(Html::icon('eye-close'), [
					'hide', 'id' => $provision->id,
				], [
					'data' => [
						'method' => 'POST',
						'confirm' => Yii::t('provision', 'Are you sure you want to hide this provision?'),
					],
					'title' => Yii::t('provision', 'Hide'),
					'aria-label' => Yii::t('provision', 'Hide'),
				]);
			},
		],
	],
	'model' => $searchModel->summary(),
]) ?>



