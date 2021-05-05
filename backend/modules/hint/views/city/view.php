<?php

use backend\widgets\GridView;
use common\models\hint\HintCity;
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
		<?= Html::a(Yii::t('hint', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('hint', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('hint', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'user',
			'statusName',
			'details:ntext',
			'created_at:date',
			'updated_at:date',
		],
	]) ?>



	<?= GridView::widget([
		'caption' => Yii::t('hint', 'Hint Sources'),
		'dataProvider' => new ActiveDataProvider([
			'query' => $model->getHintCitySources(),
			'pagination' => false,
		]),
		'columns' => [
			'source.name',
			'ratingName',
			'phone',
			'details:text',
			'created_at:date',
			'updated_at:date',
		],
	]) ?>

</div>
