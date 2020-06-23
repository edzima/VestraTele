<?php

use common\models\address\SubProvince;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model SubProvince */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Gminy', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-state-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Edytuj', ['update', 'id' => $model->id,], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Usuń', ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => 'Jesteś pewny, że chcesz usunąć?',
				'method' => 'post',
			],
		]) ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'id',
			'region',
			'state',
			'name',
		],
	]) ?>

</div>
