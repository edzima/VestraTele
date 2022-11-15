<?php

use common\models\PotentialClient;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model PotentialClient */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Potential Clients'), 'url' => ['self']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

?>
<div class="potential-client-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => 'Are you sure you want to delete this item?',
				'method' => 'post',
			],
		])
		?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'name',
			'details:ntext',
			'cityName',
			'birthday:date',
			'statusName',
			'ownerName',
			'created_at:datetime',
			[
				'attribute' => 'updated_at',
				'format' => 'datetime',
				'visible' => $model->created_at !== $model->updated_at,
			],
		],
	])

	?>

</div>
