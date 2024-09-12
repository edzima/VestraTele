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
	$("#leads-grid-pjax button[type='submit']:not(.not-selected-all)").on('click', function (){
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

		<?= Html::a(Html::faicon('bar-chart'), ['chart/index'], ['class' => 'btn btn-success', 'title' => Yii::t('lead', 'Charts')]) ?>

		<?= Html::a(Yii::t('lead', 'Search') . ' ' . Html::icon('phone'), ['phone'], [
			'class' => 'btn btn-success',
			'title' => Yii::t('lead', 'Search - Phone'),
			'aria-label' => Yii::t('lead', 'Search - Phone'),
		]) ?>


		<?= Html::a(Yii::t('lead', 'Search') . ' ' . Html::icon('user'), ['name'], [
			'class' => 'btn btn-info',
			'title' => Yii::t('lead', 'Search - Name'),
			'aria-label' => Yii::t('lead', 'Search - Name'),
		]) ?>

		<?= CreateLeadBtnWidget::widget([
			'owner_id' => is_int($searchModel->user_id) ? $searchModel->user_id : null,
		]) ?>

		<?= Html::a(Yii::t('lead', 'Lead Reports'), ['report/index'], ['class' => 'btn btn-warning']) ?>

		<span class="btn-group">
			<?= Html::a(Yii::t('lead', 'Lead Reminders'), ['reminder/index'], ['class' => 'btn btn-danger']) ?>
			<?= Html::a(Html::icon('calendar'), ['/calendar/lead-reminder/index'], ['class' => 'btn btn-danger']) ?>
		</span>

		<?= Yii::$app->user->can(Worker::PERMISSION_LEAD_COST)
			? Html::a(Yii::t('lead', 'Costs'), ['cost/index'], ['class' => 'btn btn-warning'])
			: ''
		?>

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
