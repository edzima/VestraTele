<?php

use backend\helpers\Html;
use backend\modules\settlement\models\search\IssuePayCalculationSearch;
use backend\modules\settlement\widgets\IssuePayCalculationGrid;
use common\widgets\ActiveForm;
use kartik\number\NumberControl;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel IssuePayCalculationSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('settlement', 'Settlements without provisions');

$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Without provisions');
?>
<div class="settlement-without-provisions">

	<p>
		<?= Html::a(
			Yii::t('backend', 'Generate'),
			['/provision/settlement/generate-multiple', 'ids' => $dataProvider->getKeys()],
			['class' => 'btn btn-success']
		) ?>
	</p>

	<div class="settlement-without-provisions-search">

		<?php $form = ActiveForm::begin([
			'action' => ['without-provisions'],
			'method' => 'get',
		]); ?>

		<div class="row">

			<?= $form->field($searchModel, 'fromValue', ['options' => ['class' => 'col-xs-6 col-md-3 col-lg-2']])->widget(NumberControl::class) ?>
			<?= $form->field($searchModel, 'toValue', ['options' => ['class' => 'col-xs-6 col-md-4 col-lg-2']])->widget(NumberControl::class) ?>
			<?= $form->field($searchModel, 'withArchive', ['options' => ['class' => 'col-xs-1']])->checkbox() ?>

		</div>


		<div class="form-group">
			<?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
			<?= Html::a(Yii::t('common', 'Reset'), 'index', ['class' => 'btn btn-default']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>

	<?= IssuePayCalculationGrid::widget([
		'filterModel' => $searchModel,
		'dataProvider' => $dataProvider,
		'withProblems' => false,
	]) ?>
</div>
