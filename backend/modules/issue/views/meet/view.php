<?php

use common\modules\meet\widgets\MeetDetailView;
use yii\helpers\Html;
use yii\web\YiiAsset;

/* @var $this yii\web\View */
/* @var $model common\models\issue\IssueMeet */

$this->title = $model->getClientFullName();
$this->params['breadcrumbs'][] = ['label' => 'Spotkania', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="issue-meet-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Edytuj', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Usuń', ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => 'Are you sure you want to delete this item?',
				'method' => 'post',
			],
		]) ?>
	</p>


	<?= MeetDetailView::widget([
		'model' => $model,
	]) ?>

</div>
