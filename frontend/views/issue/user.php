<?php

use frontend\models\search\IssueUserSearch;
use frontend\widgets\GridView;
use frontend\widgets\IssueColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel IssueUserSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('common', 'Issues users');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-user-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => IssueColumn::class],
			[
				'attribute' => 'surname',
				'label' => $searchModel->getAttributeLabel('surname'),
				'value' => 'user',
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => IssueUserSearch::getTypesNames(),
			],
		],
	]); ?>
</div>
