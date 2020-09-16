<?php

use common\models\user\Worker;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = 'Hierarchia';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-tree-index">
	<h1><?= Html::encode($this->title) ?></h1>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			[
				'format' => 'raw',
				'attribute' => 'fullName',
				'value' => static function (Worker $data) {
					return Html::a($data->getFullName(), ['update', 'id' => $data->id], ['target' => '_blank']);
				},
			],
			'action_at:datetime',
			'email:email',
		],

	]); ?>
</div>
