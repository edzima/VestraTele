<?php

use common\models\issue\Issue;
use frontend\models\ClientIssueSearch;
use frontend\models\IssueSearch;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $searchModel ClientIssueSearch */

$this->title = 'Szukaj sprawe';
$this->params['breadcrumbs'][] = ['label' => 'Sprawy', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="issue-client-search">

	<h1><?= Html::encode($this->title) ?></h1>

	<div class="issue-client-search-form">


		<?php $form = ActiveForm::begin([
			'action' => ['search'],
			'method' => 'get',
		]); ?>

		<h2>Klient</h2>

		<?= $form->field($searchModel, 'client_surname')->textInput() ?>


		<h2>Poszkodowany</h2>

		<?= $form->field($searchModel, 'victim_surname')->textInput() ?>


		<div class="form-group">
			<?= Html::submitButton('Szukaj', ['class' => 'btn btn-primary']) ?>
		</div>


		<?php ActiveForm::end(); ?>

	</div>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'tableOptions' => [
			'class' => 'ellipsis',
		],
		'columns' => [
			['class' => SerialColumn::class],
			[
				'class' => DataColumn::class,
				'attribute' => 'longId',
				'options' => [
					'style' => 'width:100px',
				],

			],
			[
				'class' => DataColumn::class,
				'attribute' => 'type_id',
				'filter' => IssueSearch::getTypesNames(),
				'value' => 'type.short_name',
				'contentOptions' => [
					'class' => 'bold-text text-center',
				],
				'options' => [
					'style' => 'width:80px',
				],

			],
			[
				'class' => DataColumn::class,
				'attribute' => 'stage_id',
				'filter' => $searchModel->getStagesNames(),
				'value' => 'stage.short_name',
				'contentOptions' => [
					'class' => 'bold-text text-center',
				],
				'options' => [
					'style' => 'width:60px',
				],
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'client_surname',
				'value' => 'clientFullName',
				'label' => 'Klient',
				'filter' => false,
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'victim_surname',
				'value' => 'victimFullName',
				'label' => 'Poszkodowany',
				'filter' => false,
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'created_at',
				'format' => 'date',
				'width' => '80px',
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'updated_at',
				'width' => '80px',
				'format' => 'date',
			],
			[
				'class' => ActionColumn::class,
				'template' => '{view}',
				'visibleButtons' => [
					'view' => static function (Issue $model) use ($searchModel) {
						return !$model->isArchived() || $searchModel->withArchive;
					},
				],
			],
		],
	]) ?>
</div>

