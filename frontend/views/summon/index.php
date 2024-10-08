<?php

use frontend\helpers\Html;
use frontend\helpers\Url;
use frontend\models\search\SummonSearch;
use frontend\widgets\IssueTypeHeader;
use frontend\widgets\SummonGrid;
use yii\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $searchModel SummonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Summons');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/index']];
if ($searchModel->getIssueMainType()) {
	$this->params['breadcrumbs'][] = [
		'label' => $searchModel->getIssueMainType()->name,
		'url' => Url::issuesParentType($searchModel->getIssueMainType()->id),
	];
}

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="summon-index">

	<?= IssueTypeHeader::widget([
		'navOptions' => [
			'route' => ['/summon/index', Html::getInputName($searchModel, 'type_id') => $searchModel->type_id],
		],
	]) ?>

	<p>
		<?= Nav::widget([
			'options' => ['class' => 'nav-pills'],
			'items' => $searchModel->getSummonTypeNavItems(),
		])
		?>
	</p>

	<p>
		<?= Html::a(Yii::t('issue', 'Calendar'), ['/calendar/summon-calendar/index'], [
			'class' => 'btn btn-success',
		]) ?>

		<?= Html::a(Yii::t('issue', 'Summon Docs'), [
			'/summon-doc/to-do',
			Url::PARAM_ISSUE_PARENT_TYPE => $searchModel->issueParentTypeId,
		], [
			'class' => 'btn btn-warning',
		]) ?>
	</p>

	<?= SummonGrid::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
	]) ?>


</div>
