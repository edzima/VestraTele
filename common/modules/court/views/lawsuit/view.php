<?php

use common\helpers\Breadcrumbs;
use common\helpers\Html;
use common\helpers\Url;
use common\models\issue\IssueInterface;
use common\modules\court\models\Lawsuit;
use common\modules\court\models\LawsuitSession;
use common\modules\court\modules\spi\entity\lawsuit\LawsuitViewIntegratorDto;
use common\modules\court\modules\spi\entity\notification\NotificationDTO;
use common\modules\court\widgets\LawsuitSmsBtnWidget;
use common\modules\issue\widgets\IssueNotesWidget;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\CustomerDataColumn;
use common\widgets\grid\DateTimeColumn;
use common\widgets\grid\IssueColumn;
use common\widgets\grid\IssueStageColumn;
use common\widgets\grid\IssueTypeColumn;
use common\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var Lawsuit $model */
/** @var LawsuitViewIntegratorDto|null $lawsuitDetails */
/** @var DataProviderInterface|null $notificationsDataProvider */
/** @var int|null $notificationId */

$this->title = $model->getName();

$issueDataProvider = new ActiveDataProvider([
	'query' => $model->getIssues()
		->with('customer')
		->with('tags.tagType')
		->with('entityResponsible'),
]);

if ($model->is_appeal) {
	$this->title .= ' - ' . Yii::t('court', 'Is Appeal');
}
if ($issueDataProvider->getTotalCount()) {
	$issue = $issueDataProvider->getModels()[0];
	$this->params['breadcrumbs'] = Breadcrumbs::issue($issue);
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('court', 'Lawsuits'), 'url' => ['index']];
if ($issueDataProvider->getTotalCount() === 1) {
	$this->params['breadcrumbs'][] = $model->signature_act;
} else {
	$this->params['breadcrumbs'][] = $this->title;
}
YiiAsset::register($this);
?>
<div class="court-lawsuit-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('court', 'Create Lawsuit Session'),
			['lawsuit-session/create', 'lawsuitId' => $model->id], ['class' => 'btn btn-success'])
		?>

		<?= Html::a(Yii::t('court', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('court', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('court', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<div class="row">
		<div class="col-md-5">

			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					'signature_act',
					[
						'attribute' => 'courtName',
						'value' => Html::a($model->court->name, ['court/view', 'id' => $model->court_id]),
						'format' => 'html',
					],
					[
						'attribute' => 'url',
						'visible' => !empty($model->url),
						'format' => 'url',
					],
					[
						'attribute' => 'details',
						'visible' => !empty($model->details),
					],
					'creator',
					'created_at:datetime',
					'updated_at:datetime',
				],
			]) ?>

			<?= $notificationsDataProvider ?
				GridView::widget([
					'dataProvider' => $notificationsDataProvider,
					'caption' => Yii::t('court', 'Notification'),
					'summary' => false,
					'columns' => [
						'type',
						'content',
						[
							'class' => DateTimeColumn::class,
							'attribute' => 'date',
							'contentBold' => true,
						],
						[
							'class' => ActionColumn::class,
							'template' => '{read}',
							'buttons' => [
								'read' => function ($url, NotificationDTO $model, $key) {
									if ($model->read) {
										return '';
									}
									return Html::a(
										Html::icon('check'),
										[
											'spi/notification/read',
											'id' => $model->id,
											'appeal' => $this->params['appeal'],
											'returnUrl' => Url::current(),
										], [
											'data-method' => 'POST',
										]
									);
								},
							],
						],
					],
					'rowOptions' => static function (NotificationDTO $data) use ($notificationId): array {
						$options = [];
						if (!$data->read) {
							Html::addCssClass($options, 'warning');
						}
						if ($data->read) {
							Html::addCssClass($options, 'half-transparent');
						}
						if ($notificationId && $data->id === $notificationId) {
							Html::addCssClass($options, 'success');
						}
						return $options;
					},
				])
				: ''
			?>

		</div>

		<div class="col-md-7">


			<?= GridView::widget([
				'dataProvider' => new ActiveDataProvider([
					'query' => $model->getSessions(),
				]),
				'caption' => Yii::t('court', 'Lawsuit Sessions'),
				'summary' => false,
				'columns' => [
					[
						'attribute' => 'result',
						'noWrap' => true,
					],
					[
						'attribute' => 'date_at',
						'format' => 'datetime',
						'noWrap' => true,
					],
					[
						'attribute' => 'room',
						'noWrap' => true,
					],
					[
						'attribute' => 'presenceOfTheClaimantName',
						'noWrap' => true,
					],
					[
						'attribute' => 'locationName',
						'noWrap' => true,
					],
					[
						'attribute' => 'judge',
						'noWrap' => true,
					],
					'details:ntext',
					//	'created_at:datetime',
					//	'updated_at:datetime',
					[
						'class' => ActionColumn::class,
						'controller' => 'lawsuit-session',
						'template' => '{url} {update} {delete}',
						'buttons' => [
							'url' => function ($url, LawsuitSession $model): string {
								if (!empty($model->url)) {
									return Html::a(
										Html::faicon('link')
										, $model->url, [
										'data-target' => '-blank',
									]);
								}
								return '';
							},
						],

					],

				],
			]) ?>

			<?= GridView::widget([
				'dataProvider' => $issueDataProvider,
				'summary' => false,
				'columns' => [
					[
						'class' => IssueColumn::class,
						'viewBaseUrl' => '/issue/issue/view',
					],
					[
						'class' => CustomerDataColumn::class,
						'value' => 'customer.fullName',
					],
					[
						'class' => IssueTypeColumn::class,
						'contentBold' => false,
						'valueType' => IssueTypeColumn::VALUE_NAME,

					],
					[
						'class' => IssueStageColumn::class,
						'valueType' => IssueStageColumn::VALUE_NAME,
					],
					[
						'attribute' => 'entityResponsible',
					],
					[
						'class' => ActionColumn::class,
						'template' => '{sms} {remove} {note}',
						'buttons' => [
							'sms' => function ($url, IssueInterface $issue) use ($model): string {
								return LawsuitSmsBtnWidget::widget([
									'issue' => $issue,
									'model' => $model,
								]);
							},
							'remove' => function (string $url, IssueInterface $issue) use ($model): string {
								return Html::a(
									Html::icon('remove'),
									['unlink-issue', 'id' => $model->id, 'issueId' => $issue->getIssueId()], [
									'title' => Yii::t('court', 'Delete'),
									'aria-label' => Yii::t('court', 'Delete'),
									'data-method' => 'POST',
									'data-confirm' => Yii::t('court', 'Are you sure you want to delete this Issue from Lawsuit?'),
								]);
							},
							'note' => function (string $url, IssueInterface $issue) use ($model): string {
								return Html::a(
									Html::faicon('comments'),
									['note/create-lawsuit', 'lawsuitId' => $model->id, 'issueId' => $issue->getIssueId()], [
									'title' => Yii::t('court', 'Create Note'),
									'aria-label' => Yii::t('court', 'Create Note'),
								]);
							},
						],
					],
				],
			]) ?>


			<?= IssueNotesWidget::widget([
				'notes' => $model->getNotes()->all(),
			]) ?>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">
			<?php if ($lawsuitDetails): ?>
				<?= $this->render('_spi_lawsuit_view', ['model' => $lawsuitDetails,]) ?>
			<?php endif; ?>
		</div>

	</div>


</div>
