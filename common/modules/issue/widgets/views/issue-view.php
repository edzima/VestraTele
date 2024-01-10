<?php

use backend\modules\settlement\widgets\IssueCostActionColumn;
use common\assets\TooltipAsset;
use common\helpers\Url;
use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\issue\IssueShipmentPocztaPolska;
use common\models\issue\IssueTagType;
use common\models\issue\IssueUser;
use common\models\user\User;
use common\models\user\Worker;
use common\modules\issue\widgets\IssueTagsWidget;
use common\modules\issue\widgets\IssueUsersWidget;
use common\widgets\FieldsetDetailView;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\CustomerDataColumn;
use common\widgets\grid\IssueColumn;
use common\widgets\GridView;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model Issue */
/* @var $usersLinks bool */
/* @var $claimActionColumn bool */
/* @var $relationActionColumn bool */
/* @var $userMailVisibilityCheck bool */
/* @var $costRoute string|null */
/* @var $entityUrl string|null */
/* @var $stageUrl string|null */
/* @var $typeUrl string|null */
/* @var $shipmentsActionColumn bool */

?>

<div id="issue-details">
	<div class="row">
		<div class="col-md-7 col-lg-6">

			<span class="pull-right">
				<?= IssueTagsWidget::widget([
					'containerTag' => 'span',
					'groupTag' => 'span',
					'models' => IssueTagType::viewIssuePositionFilter(
						$model->tags,
						IssueTagType::VIEW_ISSUE_BEFORE_CUSTOMERS
					),
				]) ?>
			</span>
			<div class="clearfix"></div>

			<?= IssueUsersWidget::widget([
				'model' => $model,
				'type' => IssueUsersWidget::TYPE_CUSTOMERS,
				'legendEncode' => false,
				'withCheckEmailVisibility' => $userMailVisibilityCheck,
				'withTraits' => true,
				'legend' => function (IssueUser $issueUser) use ($usersLinks, $model): string {
					$legend = Html::encode($issueUser->getTypeWithUser());

					if ($usersLinks) {
						$legend = Html::a($legend, ['/user/customer/view', 'id' => $issueUser->user_id]);
					}
					if ($issueUser->type === IssueUser::TYPE_CUSTOMER) {
						$tags = IssueTagType::viewIssuePositionFilter($model->tags, IssueTagType::VIEW_ISSUE_POSITION_CUSTOMER);
						if (!empty($tags)) {
							$legend .= IssueTagsWidget::widget([
								'models' => $tags,
								'containerTag' => 'span',
								'groupTag' => 'span',
							]);
						}
					}
					return $legend;
				},
				'afterLegend' => static function (IssueUser $issueUser) use ($usersLinks): string {

					if ($issueUser->type === IssueUser::TYPE_CUSTOMER || !$usersLinks) {
						return '';
					}
					$content = Html::beginTag('span', ['class' => 'pull-right form-group']);
					if (Yii::$app->user->can(Worker::PERMISSION_ISSUE_LINK_USER)) {
						$content .= Html::a(Html::icon('pencil'),
							[
								'/issue/user/update-type',
								'issueId' => $issueUser->issue_id,
								'userId' => $issueUser->user_id,
								'type' => $issueUser->type,
							],
							[
								'class' => 'btn btn-xs btn-primary',
								'title' => Yii::t('common', 'Update'),
								'aria-label' => Yii::t('common', 'Update'),
							]);

						$content .= ' ' . Html::a(Html::icon('trash'),
								[
									'/issue/user/delete',
									'issueId' => $issueUser->issue_id,
									'userId' => $issueUser->user_id,
									'type' => $issueUser->type,
								], [

									'class' => 'btn btn-xs btn-danger',
									'data-method' => 'POST',
									'title' => Yii::t('common', 'Delete'),
									'aria-label' => Yii::t('common', 'Delete'),
									'data-confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
								]);
					}
					$content .= Html::endTag('span');
					return $content;
				},
				'withAddress' => static function (IssueUser $issueUser): bool {
					return $issueUser->type === IssueUser::TYPE_CUSTOMER;
				},
				'withBirthday' => true,
			]) ?>

			<p>

			</p>
			<?= IssueUsersWidget::widget([
				'model' => $model,
				'type' => IssueUsersWidget::TYPE_WORKERS,
				'legendEncode' => !$usersLinks,
				'withCheckEmailVisibility' => $userMailVisibilityCheck,
				'legend' => static function (IssueUser $issueUser) use ($usersLinks): string {
					$legend = $issueUser->getTypeWithUser();
					if ($usersLinks) {
						$legend = Html::a($legend,
							['/user/worker/view', 'id' => $issueUser->user_id]);
					}
					return $legend;
				},
				'afterLegend' => static function (IssueUser $issueUser) use ($usersLinks): string {
					if (!$usersLinks) {
						return '';
					}
					$content = Html::beginTag('span', ['class' => 'pull-right form-group']);
					if (Yii::$app->user->can(Worker::ROLE_BOOKKEEPER)) {
						$content .= Html::a('<i class="fa fa-money" aria-hidden="true"></i>',
							[
								'/settlement/cost/create-installment',
								'id' => $issueUser->issue_id,
								'user_id' => $issueUser->user_id,
							], [
								'class' => 'btn btn-success btn-xs',
								'title' => Yii::t('settlement', 'Create Installment'),
								'aria-label' => Yii::t('settlement', 'Create Installment'),
							]);
					}

					if (Yii::$app->user->can(Worker::PERMISSION_ISSUE_LINK_USER)) {
						$content .= ' ' . Html::a(Html::icon('pencil'),
								[
									'/issue/user/update-type',
									'issueId' => $issueUser->issue_id,
									'userId' => $issueUser->user_id,
									'type' => $issueUser->type,
								],
								[
									'class' => 'btn btn-xs btn-primary',
									'title' => Yii::t('common', 'Update'),
									'aria-label' => Yii::t('common', 'Update'),
								]);

						$requiredTypes = [
							IssueUser::TYPE_AGENT,
							IssueUser::TYPE_LAWYER,
						];
						if (!in_array($issueUser->type, $requiredTypes)) {
							$content .= ' ' . Html::a(Html::icon('trash'),
									[
										'/issue/user/delete',
										'issueId' => $issueUser->issue_id,
										'userId' => $issueUser->user_id,
										'type' => $issueUser->type,
									], [

										'class' => 'btn btn-xs btn-danger',
										'data-method' => 'POST',
										'title' => Yii::t('common', 'Delete'),
										'aria-label' => Yii::t('common', 'Delete'),
										'data-confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
									]);
						}
					}
					$content .= Html::endTag('span');

					return $content;
				},
			]) ?>
		</div>
		<div class="col-md-5 col-lg-6">

			<?php
			if (Yii::$app->user->can(User::PERMISSION_COST)) {
				$costDataProvider = new ActiveDataProvider([
					'query' => $model->getCosts()
						->with('user.userProfile'),
				]);
				echo GridView::widget([
					'dataProvider' => $costDataProvider,
					'caption' => Yii::t('settlement', 'Costs')
						. ($costRoute
							?
							Html::a(Html::icon('plus'), [$costRoute . '/create', 'id' => $model->id], [
								'class' => 'btn btn-warning pull-right',
							])
							: ''),
					'summary' => '',
					'emptyText' => '',
					'showPageSummary' => $costDataProvider->totalCount > 1,
					'columns' => [
						[
							'attribute' => 'type',
							'value' => 'typeName',
						],
						[
							'attribute' => 'user_id',
							'value' => 'user',
						],
						[
							'attribute' => 'value',
							'format' => 'currency',
							'pageSummary' => true,
						],
						'deadline_at:date',
						'settled_at:date',
						[
							'class' => IssueCostActionColumn::class,
							'visible' => $costRoute !== null,
							'issue' => false,
						],
					],
				]);
			}

			?>


			<?= GridView::widget([
				'dataProvider' => new ActiveDataProvider([
					'query' => $model->getLinkedIssues()
						->with('customer')
						->with('tags')
						->with('tags.tagType'),
				]),
				'summary' => '',
				'caption' => Yii::t('issue', 'Linked'),
				'emptyText' => '',
				'showOnEmpty' => false,
				'columns' => [
					[
						'class' => IssueColumn::class,
						'viewBaseUrl' => 'view',
						'tags' => static function (IssueInterface $issue): array {
							return IssueTagType::linkIssuesGridPositionFilter(
								$issue->getIssueModel()->tags,
								IssueTagType::LINK_ISSUES_GRID_POSITION_COLUMN_ISSUE_BOTTOM
							);
						},
					],
					[
						'label' => Yii::t('issue', 'Type'),
						'attribute' => 'typeName',
					],
					[
						'label' => Yii::t('issue', 'Stage'),
						'format' => 'raw',
						'value' => function (IssueInterface $issue): string {
							return Html::tag(
								'span',
								Html::encode($issue->getIssueModel()->getStageName()),
								[TooltipAsset::DEFAULT_ATTRIBUTE_NAME => Yii::$app->formatter->asDate($issue->getIssueModel()->stage_change_at)]
							);
						},
					],
					[
						'label' => Yii::t('issue', 'Stage Deadline At'),
						'format' => 'date',
						'attribute' => 'stage_deadline_at',
					],
					[
						'class' => CustomerDataColumn::class,
						'value' => 'customer.fullName',
						'tags' => static function (IssueInterface $issue): array {
							return IssueTagType::linkIssuesGridPositionFilter(
								$issue->getIssueModel()->tags,
								IssueTagType::LINK_ISSUES_GRID_POSITION_COLUMN_CUSTOMER_BOTTOM
							);
						},
					],
					[
						'class' => ActionColumn::class,
						'controller' => '/issue/relation',
						'template' => '{tags} {update} {delete}',
						'visible' => $relationActionColumn,
						'buttons' => [
							'tags' => function (string $url): string {
								return Html::a(Html::icon('tags'), $url, [
									'title' => Yii::t('issue', 'Tags'),
									'aria-label' => Yii::t('issue', 'Tags'),
								]);
							},
							'delete' => function (string $url): string {
								return Html::a(Html::icon('remove'), $url, [
									'title' => Yii::t('issue', 'Unlink'),
									'aria-label' => Yii::t('issue', 'Unlink'),
									'data-method' => 'POST',
								]);
							},
						],
						'urlCreator' => static function (string $action, IssueInterface $issue) use ($model): string {
							switch ($action) {
								case 'delete':
									return Url::to([
										'/issue/relation/delete',
										'id' => $model->getIssueRelationId($issue->getIssueId()),
										'returnUrl' => Url::current(),
									]);
								case 'update':
									return Url::to(['/issue/issue/update', 'id' => $issue->getIssueId()]);
								case 'tags':
									return Url::to([
										'/issue/tag/issue',
										'issueId' => $issue->getIssueId(),
										'returnUrl' => Url::current(),
									]);
							}
							return '';
						},
					],
				],
			]) ?>

			<?= GridView::widget([
				'dataProvider' => new ActiveDataProvider([
					'query' => $model->getClaims(),
				]),
				'summary' => '',
				'caption' => Yii::t('issue', 'Issue Claims'),
				'emptyText' => '',
				'showOnEmpty' => false,
				'columns' => [
					'typeName',
					'entityResponsible.name:text:' . Yii::t('issue', 'Entity'),
					'trying_value:currency:' . Yii::t('issue', 'Claim'),
					'percent_value',
					'obtained_value:currency:' . Yii::t('issue', 'Obtained'),
					'details:ntext',
					'date:date',
					[
						'class' => ActionColumn::class,
						'controller' => '/issue/claim',
						'template' => '{update} {delete}',
						'visible' => $claimActionColumn,
					],
				],
			]) ?>


			<?= FieldsetDetailView::widget([
				'legend' => Yii::t('common', 'Issue details'),
				'legendOptions' => [
					'encode' => false,
				],
				'afterLegend' => IssueTagsWidget::widget([
					'position' => IssueTagsWidget::POSITION_ISSUE_DETAIL_BEFORE,
					'models' => $model->tags,
					'groupLabel' => true,
				]),
				'afterDetail' => IssueTagsWidget::widget([
					'position' => IssueTagsWidget::POSITION_ISSUE_DETAIL_AFTER,
					'models' => $model->tags,
					'groupLabel' => true,
				]),
				'toggle' => false,
				'detailConfig' => [
					'id' => 'base-details',
					'model' => $model,
					'options' => [
						'class' => 'table table-striped table-bordered detail-view th-nowrap',
					],
					'attributes' => [
						[
							'attribute' => 'signature_act',
							'visible' => !empty($model->signature_act),
							'format' => 'ntext',
						],
						[
							'label' => Yii::t('common', 'Created at / Updated at'),
							'value' => function (Issue $model): string {
								$content = Html::tag('span',
									Yii::$app->formatter->asDate($model->created_at), [
										TooltipAsset::DEFAULT_ATTRIBUTE_NAME => Yii::$app->formatter->asTime($model->created_at),
									]
								);
								$content .= ' / ';
								$content .= Html::tag('strong',
									Yii::$app->formatter->asDate($model->updated_at), [
										TooltipAsset::DEFAULT_ATTRIBUTE_NAME => Yii::$app->formatter->asTime($model->updated_at),
									]
								);
								return $content;
							},
							'format' => 'raw',
						],

						[
							'attribute' => 'archives_nr',
							'visible' => $model->isArchived(),
						],
						[
							'attribute' => 'type',
							'format' => $typeUrl ? 'html' : 'text',
							'label' => $model->getAttributeLabel('type_id'),
							'value' => $typeUrl
								? Html::a(Html::encode($model->type->name), $typeUrl)
								: $model->type->name,
						],
						[
							'attribute' => 'stage',
							'label' => $model->getAttributeLabel('stage_id'),
							'format' => $stageUrl ? 'html' : 'text',
							'value' => function (IssueInterface $issue) use ($stageUrl): string {
								$label = !empty($issue->getIssueModel()->stage_change_at)
									? Yii::t('issue', '{stage} ({from})', [
										'stage' => $issue->getIssueStage()->name,
										'from' => Yii::$app->formatter->asDate($issue->getIssueModel()->stage_change_at),
									])
									: $issue->getIssueStage()->name;
								if ($stageUrl) {
									return Html::a(Html::encode($label), $stageUrl);
								}
								return $label;
							},
						],
						[
							'attribute' => 'stage_deadline_at',
							'label' => $model->getAttributeLabel('stage_deadline_at'),
							'format' => 'date',
							'visible' => !empty($model->getIssueModel()->stage_deadline_at),
						],
						[
							'label' => $model->getAttributeLabel('entity_responsible_id'),
							'format' => $entityUrl ? 'html' : 'text',
							'value' => $entityUrl
								? Html::a(
									Html::encode($model->entityResponsible->name), $entityUrl
								)
								: $model->entityResponsible->name,

						],

						'signing_at:date',
						[
							'attribute' => 'type_additional_date_at',
							'format' => 'date',
							'visible' => !empty($model->type_additional_date_at),
						],
						[
							'attribute' => 'details',
							'format' => 'html',
							'visible' => !empty($model->details),
						],

					],

				],
			]) ?>

			<?=
			GridView::widget([
				'dataProvider' => new ActiveDataProvider([
					'query' => $model->getIssueModel()->getShipmentsPocztaPolska(),
				]),
				'showOnEmpty' => false,
				'summary' => '',
				'caption' => Yii::t('issue', 'Issue Shipment Poczta Polska'),
				'emptyText' => false,
				'columns' => [
					'shipmentTypeName',
					'details:ntext',
					[
						'attribute' => 'shipment_number',
						'format' => 'raw',
						'value' => function (IssueShipmentPocztaPolska $model): string {
							return Html::a(
								Html::encode($model->shipment_number),
								Yii::$app->pocztaPolska->externalTrackingUrl($model->shipment_number),
								[
									'target' => '_blank',
								]
							);
						},
					],
					'shipment_at:date',
					'finished_at:date',
					'created_at:date',
					'updated_at:date',
					[
						'class' => ActionColumn::class,
						'visible' => $shipmentsActionColumn,
						'controller' => 'shipment-poczta-polska',
						'template' => '{refresh} {view} {update} {delete}',
						'buttons' => [
							'refresh' => function (string $url, IssueShipmentPocztaPolska $model): string {
								if ($model->isFinished()) {
									return '';
								}
								$url = Url::to([
									'shipment-poczta-polska/refresh',
									'issue_id' => $model->issue_id,
									'shipment_number' => $model->shipment_number,
									'returnUrl' => Url::current(),
								]);
								return Html::a(
									Html::icon('refresh'),
									$url, [
										'data-method' => 'POST',
									]
								);
							},
						],
					],
				],
			])
			?>

		</div>
	</div>


</div>

