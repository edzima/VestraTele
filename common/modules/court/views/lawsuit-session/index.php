<?php

use common\modules\court\models\LawsuitSession;
use common\modules\court\models\search\LawsuitSessionSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var LawsuitSessionSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('court', 'Lawsuit Sessions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('court', 'Lawsuits'), 'url' => ['lawsuit/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lawsuit-session-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('court', 'Create Lawsuit Session'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'lawsuitSignature',
				'contentBold' => true,
				'value' => function (LawsuitSession $model): string {
					return Html::a(
						Html::encode($model->lawsuit->signature_act),
						['lawsuit/view', 'id' => $model->lawsuit_id]
					);
				},
				'noWrap' => true,
				'label' => Yii::t('court', 'Signature Act'),
				'format' => 'raw',
			],
			[
				'attribute' => 'lawsuitCourtName',
				'value' => function (LawsuitSession $model): string {
					return Html::a(
						Html::encode($model->lawsuit->court->name),
						['court/view', 'id' => $model->lawsuit->court_id]
					);
				},
				'label' => Yii::t('court', 'Court'),
				'format' => 'raw',
			],
			'details:ntext',
			'date_at:datetime',
			'created_at:datetime',
			'updated_at:datetime',
			//'room',
			'is_cancelled:boolean',
			//'presence_of_the_claimant',
			[
				'class' => ActionColumn::className(),
				'urlCreator' => function ($action, LawsuitSession $model, $key, $index, $column) {
					return Url::toRoute([$action, 'id' => $model->id]);
				},
			],
		],
	]); ?>


</div>
