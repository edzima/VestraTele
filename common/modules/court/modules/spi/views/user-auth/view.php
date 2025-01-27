<?php

use common\modules\court\modules\spi\models\SpiUserAuth;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var SpiUserAuth $model */

$this->title = Yii::t('spi', '{user} - SPI Auth', [
	'user' => $model->user->getFullName(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('spi', 'SPI User Auths'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="spi-user-auth-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('spi', 'Check Auth'), ['check-auth', 'id' => $model->id], [
			'class' => 'btn btn-warning',
			'data-method' => 'post',
		]) ?>
		<?= Html::a(Yii::t('spi', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('spi', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('spi', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'username',
			'last_action_at:datetime',
			'created_at:datetime',
			'updated_at:datetime',
		],
	]) ?>

</div>
