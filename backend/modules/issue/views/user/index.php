<?php

use backend\modules\issue\models\search\UserSearch;
use backend\widgets\IssueColumn;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel UserSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('backend', 'Issue users');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issues'), 'url' => ['/issue/issue/index']];

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
				'attribute' => 'userSurname',
				'value' => 'user',
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => UserSearch::getTypesNames(),
			],
		],
	]); ?>
</div>
