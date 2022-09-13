<?php

use common\helpers\Html;
use common\models\user\Worker;
use common\modules\lead\models\searches\LeadSearch;
use common\modules\lead\widgets\CreateLeadBtnWidget;

/* @var $this yii\web\View */
/* @var $searchModel LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $onlyUser bool */
/* @var $assignUsers bool */
/* @var $visibleButtons array */

$this->title = Yii::t('lead', 'Leads');
$this->params['breadcrumbs'][] = $this->title;

$js = <<< JS

function ensureSelectionAllOnEmpty(){
	$("#leads-grid-pjax button[type='submit']").on('click', function (){
		 if($('#leads-grid').yiiGridView('getSelectedRows').length===0){
			$('#leads-grid [name="selection_all"]').click(); 
		 }
	});

}

ensureSelectionAllOnEmpty();

$(document).on('pjax:success', function() {

	ensureSelectionAllOnEmpty();
	$("html, body").animate({ scrollTop: $("#leads-grid").position().top }, 300);
	const gridInputs = document.querySelectorAll("#leads-grid [name^='LeadSearch']");
	const searchInputs = document.querySelectorAll("#lead-header-filter-form [name^='LeadSearch']");
	for (var i = 0; i < gridInputs.length; ++i) {
		var input = gridInputs[i];
		for (var j = 0; j < searchInputs.length; ++j) {
			var searchInput = searchInputs[j];
			if(searchInput.name === input.name){
				searchInput.value = input.value;
			}
		}
	}
	
	
});

JS;

$this->registerJs($js);

?>
<div class="lead-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p style="display: inline">
		<?= Html::a(Yii::t('lead', 'Phone Lead'), ['phone'], ['class' => 'btn btn-info']) ?>

		<?= CreateLeadBtnWidget::widget([
			'owner_id' => is_int($searchModel->user_id) ? $searchModel->user_id : null,
		]) ?>

		<?= Html::a(Yii::t('lead', 'Lead Reports'), ['report/index'], ['class' => 'btn btn-warning']) ?>

		<?= Html::a(Yii::t('lead', 'Lead Reminders'), ['reminder/index'], ['class' => 'btn btn-danger']) ?>

		<?= Yii::$app->user->can(Worker::PERMISSION_LEAD_DIALER_MANAGER)
			? Html::a(Yii::t('lead', 'Dialers'), ['dialer/index'], ['class' => 'btn btn-primary'])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_LEAD_DUPLICATE)
			? Html::a(Yii::t('lead', 'Duplicates'), ['duplicate/index'], ['class' => 'btn btn-warning'])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_LEAD_MARKET)
			? Html::a(Yii::t('lead', 'Lead Markets'), ['market/index'], ['class' => 'btn btn-success'])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_LEAD_DELETE)
		&& $dataProvider->pagination->pageCount > 1
			?
			Html::a(
				Yii::t('lead', 'Delete ({count})', [
					'count' => $dataProvider->getTotalCount(),
				]),
				false,
				[
					'class' => 'btn btn-danger pull-right',
					'data' => [
						'method' => 'delete',
						'confirm' => Yii::t('lead', 'Are you sure you want to delete this items?'),
					],
				])

			: ''
		?>

	</p>

	<?= $this->render('_search', ['model' => $searchModel]) ?>


	<?= $this->render('_grid', [
		'dataProvider' => $dataProvider,
		'searchModel' => $searchModel,
		'assignUsers' => $assignUsers,
		'visibleButtons' => $visibleButtons,
	]) ?>


</div>
