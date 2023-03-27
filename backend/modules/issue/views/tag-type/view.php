<?php

use backend\helpers\Html;
use backend\modules\issue\models\search\IssueSearch;
use backend\widgets\GridView;
use common\models\issue\IssueTag;
use common\models\issue\IssueTagType;
use yii\data\ActiveDataProvider;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model IssueTagType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['url' => ['issue/index'], 'label' => Yii::t('issue', 'Issues')];
$this->params['breadcrumbs'][] = ['url' => ['tag/index'], 'label' => Yii::t('issue', 'Tags')];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issue Tag Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="issue-tag-type-view">

	<p>
		<?= Html::a(Yii::t('backend', 'Tags'), ['tags', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
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
			'name',
			[
				'attribute' => 'background',
				'contentOptions' => [
					'style' => [
						'color' => $model->background,
					],
				],
			],
			[
				'attribute' => 'color',
				'contentOptions' => [
					'style' => [
						'color' => $model->color,
					],
				],
			],
			'css-class',
			'sort_order',
			'viewIssuePositionName',
			'issuesGridPositionName',
			'issuesCount',
		],
	]) ?>

	<?= GridView::widget([
		'dataProvider' => new ActiveDataProvider([
			'query' => $model->getIssueTags(),
		],
		),
		'columns' => [
			[
				'attribute' => 'name',
				'format' => 'html',
				'value' => function (IssueTag $data): string {
					return Html::a(Html::encode($data->name), [
						'tag/view', 'id' => $data->id,
					]);
				},
			],
			'issuesCount',
			'is_active:boolean',
		],
	]) ?>

</div>
