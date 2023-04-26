<?php

use common\models\PotentialClient;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var PotentialClient $model */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Potential Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="potential-client-view">

	<h1><?= Html::encode($this->title) ?></h1>

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
			'id',
			'firstname',
			'lastname',
			'details:ntext',
			'city_id',
			'birthday',
			'status',
			'created_at',
			'updated_at',
			'owner_id',
			'phone',
		],
	]) ?>

</div>
