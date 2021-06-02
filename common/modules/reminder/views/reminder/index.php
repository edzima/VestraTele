<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\reminder\models\searches\ReminderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('reminder', 'Reminders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reminder-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('reminder', 'Create Reminder'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'id',
			'priority',
			'created_at',
			'updated_at',
			'date_at',
			//'details',

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
