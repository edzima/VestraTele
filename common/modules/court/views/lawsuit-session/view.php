<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\modules\court\models\LawsuitSession $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('court', 'Lawsuit Sessions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="lawsuit-session-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('court', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('court', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('court', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'id',
			'details:ntext',
			'lawsuit_id',
			'date_at',
			'created_at',
			'updated_at',
			'room',
			'is_cancelled',
			'presence_of_the_claimant',
		],
	]) ?>

</div>
