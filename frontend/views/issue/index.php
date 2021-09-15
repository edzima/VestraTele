<?php

use common\models\issue\IssueUser;
use common\models\user\User;
use common\models\user\Worker;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\CustomerDataColumn;
use common\widgets\grid\DataColumn;
use common\widgets\grid\IssueTypeColumn;
use frontend\models\search\IssueSearch;
use frontend\widgets\IssueColumn;
use kartik\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel IssueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Issues');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('frontend', 'Search issue user'), 'user', ['class' => 'btn btn-info']) ?>
		<?= Html::a(Yii::t('frontend', 'Yours settlements'), '/settlement/index', ['class' => 'btn btn-success']) ?>
		<?= Html::a(Yii::t('settlement', 'Pays'), '/pay/index', ['class' => 'btn btn-success']) ?>

		<?= Yii::$app->user->can(Worker::PERMISSION_PAY_RECEIVED)
			? Html::a(Yii::t('settlement', 'Received pays'), '/pay-received/index', ['class' => 'btn btn-primary'])
			: ''
		?>
	</p>
	<?php Pjax::begin(); ?>
	<?= $this->render('_search', ['model' => $searchModel]); ?>


	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'tableOptions' => [
			'class' => 'ellipsis',
		],
		'columns' => [
			['class' => SerialColumn::class],
			[
				'class' => IssueColumn::class,
			],
			[
				'class' => IssueTypeColumn::class,
				'valueType' => IssueTypeColumn::VALUE_NAME,
				'attribute' => 'type_id',
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'stage_id',
				'label' => $searchModel->getAttributeLabel('stage_id'),
				'filter' => $searchModel->getStagesNames(),
				'value' => 'issue.stage.short_name',
				'contentOptions' => [
					'class' => 'bold-text text-center',
				],
				'options' => [
					'style' => 'width:60px',
				],
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'entity_responsible_id',
				'label' => $searchModel->getAttributeLabel('entity_responsible_id'),
				'filter' => IssueSearch::getEntityNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'allowClear' => true,
					],
					'options' => [
						'placeholder' => $searchModel->getAttributeLabel('entity_responsible_id'),
					],
				],
				'value' => 'issue.entityResponsible.name',
				'options' => [
					'style' => 'width:200px',
				],
			],
			[
				'class' => DataColumn::class,
				'filterType' => GridView::FILTER_SELECT2,
				'attribute' => 'agent_id',
				'label' => $searchModel->getAttributeLabel('agent_id'),
				'value' => 'issue.agent.fullName',
				'filter' => $searchModel->getAgentsList(),
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'allowClear' => true,
						'width' => '180px',
					],
					'options' => [
						'placeholder' => $searchModel->getAttributeLabel('agent_id'),
					],
				],
			],
			[
				'class' => CustomerDataColumn::class,
				'value' => 'issue.customer.fullName',
			],
			[
				'attribute' => 'customerPhone',
				'value' => 'issue.customer.profile.phone',
				'format' => 'tel',
				'label' => Yii::t('common', 'Phone number'),
				'noWrap' => true,
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'issue.created_at',
				'format' => 'date',
				'width' => '80px',
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'issue.updated_at',
				'width' => '80px',
				'format' => 'date',
			],
			[
				'class' => ActionColumn::class,
				'template' => '{note} {view}',
				'visibleButtons' => [
					'view' => static function (IssueUser $model) use ($searchModel) {
						return !$model->issue->isArchived() || $searchModel->withArchive;
					},
					'note' => Yii::$app->user->can(User::PERMISSION_NOTE),
				],
				'buttons' => [
					'note' => static function (string $url, IssueUser $model): string {
						return Html::a('<i class="fa fa-comments" aria-hidden="true"></i>',
							['note/issue', 'id' => $model->issue_id],
							[
								'title' => Yii::t('issue', 'Create Issue Note'),
								'aria-label' => Yii::t('issue', 'Create Issue Note'),
							]
						);
					},
				],
			],
		],
	]) ?>
	<?php Pjax::end(); ?>
</div>
