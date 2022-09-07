<?php

use common\models\hint\HintCity;
use common\widgets\grid\ActionColumn;
use frontend\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model HintCity */

$this->title = $model->getCityNameWithType();
$this->params['breadcrumbs'][] = ['label' => Yii::t('hint', 'Hint Cities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="hint-city-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('hint', 'Create Hint Source'), ['/hint-city-source/create', 'id' => $model->id], ['class' => 'btn btn-success']) ?>

		<?= Html::a(Yii::t('hint', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'user',
			'statusName',
			'details:ntext',
		],
	]) ?>


	<?= GridView::widget([
		'caption' => Yii::t('hint', 'Hint Sources'),
		'dataProvider' => new ActiveDataProvider([
			'query' => $model->getHintCitySources()->with('source'),
			'pagination' => false,
		]),
		'columns' => [
			'source.name',
			'statusName',
			'phone',
			'ratingName',
			'details:text',
			'created_at:date',
			'updated_at:date',
			[
				'class' => ActionColumn::class,
				'controller' => 'hint-city-source',
				'template' => '{update} {delete}',
			],
		],
	]) ?>

</div>
