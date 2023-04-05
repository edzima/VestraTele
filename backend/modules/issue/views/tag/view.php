<?php

use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\issue\IssueTag;
use common\widgets\grid\CustomerDataColumn;
use common\widgets\grid\IssueStageColumn;
use common\widgets\grid\IssueTypeColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model IssueTag */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issue Tags'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="issue-tag-view">

	<p>
		<?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			[
				'attribute' => 'tagType',
				'format' => 'html',
				'value' => $model->tagType
					? Html::a(Html::encode($model->tagType->name), [
						'tag-type/view', 'id' => $model->tagType->id,
					])
					: null,
			],
			'description',
			'is_active:boolean',
			'issuesCount',
		],
	]) ?>

	<?= GridView::widget([
			'dataProvider' => new ActiveDataProvider([
				'query' => $model->getIssues()
					->with('customer.userProfile'),
			]),
			'columns' => [
				[
					'class' => IssueColumn::class,
				],
				[
					'class' => IssueTypeColumn::class,
				],
				[
					'class' => IssueStageColumn::class,
				],
				[
					'class' => CustomerDataColumn::class,
					'value' => 'customer.fullName',
				],

			],
		]
	)

	?>

</div>
