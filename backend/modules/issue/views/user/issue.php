<?php

use backend\modules\issue\models\search\UserSearch;
use common\models\issue\Issue;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model Issue */
/* @var $searchModel UserSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('backend', 'Users in: {issue}', ['issue' => $model]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => $model, 'url' => ['/issue/issue/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issue users')];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-user-issue">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('backend', 'Link customer to issue'), ['link', 'id' => $model->id], ['class' => 'btn btn-success']) ?>

	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			'user',
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => UserSearch::getTypesNames(),
			],
		],
	]); ?>
</div>
