<?php

use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use  kartik\grid\GridView;
use kartik\export\ExportMenu;

use common\models\AnswerTyp;
use common\models\User;
use common\models\AccidentTyp;
use common\models\Wojewodztwa;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TaskStatusSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Twoje spotkania';
$this->params['breadcrumbs'][] = $this->title;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TaskStatusSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$columns = [
	'id',
	/*
	[	 'class' => '\kartik\grid\DataColumn',
		 'attribute' => 'tele',
		 'value' => 'tele.username',
		 'label' => 'Telemarketer',
		 'filter' => ArrayHelper::map(User::find()->where(['typ_work' => 'T'])->all(), 'id', 'username')
	],
	*/
	[
		'class' => '\kartik\grid\DataColumn',
		'attribute' => 'date',
	],
	[
		'class' =>
			'\kartik\grid\DataColumn',
		'attribute' => 'victim_name',
	],
	[
		'class' =>
			'\kartik\grid\DataColumn',
		'attribute' => 'phone',
	],
	[
		'class' =>
			'\kartik\grid\DataColumn',
		'attribute' => 'accident',
		'value' => 'accident.name',
		'label' => 'Zdarzenie',
		'filter' => ArrayHelper::map(AccidentTyp::find()->all(), 'id', 'name'),
	],
	[
		'class' =>
			'\kartik\grid\DataColumn',
		'attribute' => 'details',
		'contentOptions' => ['style' => 'width:30px; white-space: normal;'],

	],
	[
		'class' =>
			'\kartik\grid\DataColumn',
		'attribute' => 'wojewodztwo',
		'value' => 'wojewodztwo.name',
		'filter' => ArrayHelper::map(Wojewodztwa::find()->all(), 'id', 'name'),
	],
	[
		'class' =>
			'\kartik\grid\DataColumn',
		'attribute' => 'powiatRel',
		'value' => 'powiatRel.name',
		'label' => 'Powiat',
	],
	[
		'class' =>
			'\kartik\grid\DataColumn',
		'attribute' => 'gminaRel',
		'value' => 'gminaRel.name',
		'label' => 'Gmina',
	],
	[
		'class' =>
			'\kartik\grid\DataColumn',
		'attribute' => 'miasto',
		'value' => 'miasto.name',
	],
	'street',
	'city_code',
];

$exportMenu = ExportMenu::widget([
	'dataProvider' => $dataProvider,
	'columns' => $columns,
	'pjaxContainerId' => 'kv-pjax-container',
	'target' => ExportMenu::TARGET_SELF,
	'showConfirmAlert' => false,
	'onInitSheet' => function (Worksheet $sheet, $grid) {
		$sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
	},

	'fontAwesome' => true,
	'styleOptions' => [
		'font' => [
			'bold' => true,
			'color' => [
				'argb' => 'FFA0A0A0',
			],
		],
		'fill' => [
			'type' => Fill::FILL_GRADIENT_LINEAR,
			'startcolor' => [
				'argb' => 'FFA0A0A0',
			],
			'endcolor' => [
				'argb' => 'FFFFFFFF',
			],
		],
	],

]);

?>
<?= Html::button('Filtry', ['class' => 'btn btn-primary mg-15', 'onclick' => "$('#filter').toggle('drop');"]) ?>
<div id="filter">
	<?php echo $this->render('_search', ['model' => $searchModel]); ?>
</div>
<div class="task-status-index">
	<?php
	echo GridView::widget([
		'id' => 'kv-grid-demo',
		'dataProvider' => $dataProvider,
		'columns' => [
			[

				'class' => 'yii\grid\ActionColumn',
				'template' => '{see}',
				'buttons' => [
					'see' => function ($url, $model, $key) {
						$options = [
							'title' => 'Raportuj',
							'aria-label' => 'Raportuj',
							'data-pjax' => '0',
							'target' => '_blank',
						];

						return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::toRoute(['task-status/raport', 'id' => $key]), $options);
					},
				],
				'visible' => Yii::$app->user->can(User::ROLE_AGENT),
			],
			[
				'class' =>
					'\kartik\grid\CheckboxColumn',
				'contentOptions' => ['class' => 'kv-row-select'],
				'headerOptions' => ['class' => 'kv-all-select'],
			],
			'id',
			[
				'class' => '\kartik\grid\DataColumn',
				'attribute' => 'tele',
				'value' => 'tele.username',
				'label' => 'Telemarketer',
				'filter' => ArrayHelper::map(User::find()->where(['typ_work' => 'T'])->all(), 'id', 'username'),
			],
			[
				'class' => '\kartik\grid\DataColumn',
				'attribute' => 'date',
				'contentOptions' => ['style' => 'white-space: pre-wrap;'],
			],

			[
				'class' =>
					'\kartik\grid\DataColumn',
				'attribute' => 'victim_name',
				'contentOptions' => ['style' => 'white-space: normal;'],
			],
			[
				'class' =>
					'\kartik\grid\DataColumn',
				'attribute' => 'phone',
			],
			[
				'class' =>
					'\kartik\grid\DataColumn',
				'attribute' => 'accident',
				'value' => 'accident.name',
				'label' => 'Zdarzenie',
				'filter' => ArrayHelper::map(AccidentTyp::find()->all(), 'id', 'name'),
				'contentOptions' => ['style' => 'white-space: normal;'],
			],
			[
				'class' =>
					'\kartik\grid\DataColumn',
				'attribute' => 'details',
				'contentOptions' => ['style' => 'width:200px; white-space: normal;'],
			],
			[
				'class' =>
					'\kartik\grid\DataColumn',
				'attribute' => 'wojewodztwo',
				'value' => 'wojewodztwo.name',
				'filter' => ArrayHelper::map(Wojewodztwa::find()->all(), 'id', 'name'),
			],
			[
				'class' =>
					'\kartik\grid\DataColumn',
				'attribute' => 'powiatRel',
				'value' => 'powiatRel.name',
				'label' => 'Powiat',
			],
			[
				'class' =>
					'\kartik\grid\DataColumn',
				'attribute' => 'gminaRel',
				'value' => 'gminaRel.name',
				'label' => 'Gmina',
			],
			[
				'class' =>
					'\kartik\grid\DataColumn',
				'attribute' => 'miasto',
				'value' => 'miasto.name',
			],
			'street',
			'city_code',
			[
				'class' =>
					'\kartik\grid\DataColumn',
				'attribute' => 'answer',
				'value' => 'taskstatus.answer.name',
				'label' => 'Efekt',
				'filter' => ArrayHelper::map(AnswerTyp::find()->all(), 'id', 'name'),
			],
			[
				'class' =>
					'\kartik\grid\DataColumn',
				'attribute' => 'taskstatus.count_agreement',
			],

		],

		'filterModel' => $searchModel,
		//'columns'=>$gridColumns,

		'headerRowOptions' => ['class' => 'kartik-sheet-style'],
		'filterRowOptions' => ['class' => 'kartik-sheet-style'],
		'pjax' => true, // pjax is set to always true for this demo
		'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container']],
		// set your toolbar
		'toolbar' => [
			[
				'content' =>
					Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('kvgrid', 'Reset Grid')]),
			],
			'{toggleData}',
			$exportMenu,
			Html::button('<i class="glyphicon glyphicon-download-alt"></i> Tylko zaznaczone',
				[
					'class' => 'btn btn-default',
					'onclick' => 'var key = $("#kv-grid-demo").yiiGridView("getSelectedRows");window.location.href = "?key="+key;',
					'data-toggle' => 'tooltip',
					'title' => Yii::t('app', 'Create New Record'),
				]

			),
		],

		// set export properties
		'export' => [
			'fontAwesome' => true,
		],
		'bordered' => true,
		'striped' => false,
		'condensed' => true,
		'responsive' => true,
		'hover' => false,

		'panel' => [
			'type' => GridView::TYPE_PRIMARY,
			'heading' => '<i class="glyphicon glyphicon-book"></i>  Umówione spotkania',

		],
		//'exportConfig'=>$exportConfig,
	]);
	?>


</div>
