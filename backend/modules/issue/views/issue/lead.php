<?php

use backend\helpers\Html;
use backend\modules\issue\models\search\IssueLeadsSearch;
use backend\widgets\IssueColumn;
use common\widgets\ActiveForm;
use yii\data\ActiveDataProvider;

/* @var $this \yii\web\View */
/* @var $searchModel IssueLeadsSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('issue', 'Issue Leads');

?>


<?php $form = ActiveForm::begin([
	'action' => ['lead'],
	'method' => 'get',
]); ?>

<?= $form->field($searchModel, 'status_id')->dropDownList(\common\modules\lead\models\LeadStatus::getNames()) ?>

<div class="form-group">
	<?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
	<?= Html::a(Yii::t('backend', 'Reset'), 'index', ['class' => 'btn btn-default']) ?>
</div>

<?php ActiveForm::end(); ?>



<?= \backend\widgets\GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'columns' => [
		[
			'class' => IssueColumn::class,
		],
		[
			'attribute' => 'lead_id',
			'value' => function ($user): string {
				return Html::a($user->lead_id, ['/lead/lead/view', 'id' => $user->lead_id]);
			},
			'format' => 'html',
		],

	],
]) ?>
