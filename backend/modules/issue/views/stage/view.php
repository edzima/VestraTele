<?php

use backend\modules\issue\models\IssueStage;
use backend\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model IssueStage */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Stages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

YiiAsset::register($this);

?>
<div class="issue-stage-view">


	<p>
		<?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('backend', 'Are you sure you want to delete this Stage with all Issues with then?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'name',
			'short_name',
			[
				'format' => 'html',
				'value' => Html::ul($model->types),
				'label' => Yii::t('issue', 'Issues Types'),
			],
			[
				'attribute' => 'days_reminder',
				'visible' => $model->days_reminder !== null,
			],
			[
				'attribute' => 'calendar_background',
				'visible' => $model->calendar_background !== null,
				'contentOptions' => [
					'style' => [
						'background-color' => $model->calendar_background,
					],
				],
			],
		],
	]) ?>


	<?= GridView::widget([
		'dataProvider' => new ActiveDataProvider([
			'query' => $model->getStageTypes(),
			'pagination' => false,
		]),
		'columns' => [
			'typeName',
			'days_reminder',
			'calendar_background',
		],
	]) ?>
</div>
