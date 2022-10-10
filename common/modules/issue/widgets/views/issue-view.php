<?php

use common\helpers\Url;
use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\issue\IssueTag;
use common\models\issue\IssueUser;
use common\models\user\Worker;
use common\modules\issue\widgets\IssueUsersWidget;
use common\widgets\FieldsetDetailView;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model Issue */
/* @var $usersLinks bool */
/* @var $claimActionColumn bool */
/* @var $relationActionColumn bool */
/* @var $userMailVisibilityCheck bool */

?>

<div id="issue-details">
	<div class="row">
		<div class="col-md-7 col-lg-6">
			<?= IssueUsersWidget::widget([
				'model' => $model,
				'type' => IssueUsersWidget::TYPE_CUSTOMERS,
				'legendEncode' => !$usersLinks,
				'withCheckEmailVisibility' => $userMailVisibilityCheck,
				'withTraits' => true,
				'legend' => function (IssueUser $issueUser) use ($usersLinks, $model): string {
					$legend = Html::encode($issueUser->getTypeWithUser());
					if ($issueUser->type === IssueUser::TYPE_CUSTOMER) {
						$tags = IssueTag::typeFilter($model->tags, IssueTag::TYPE_CLIENT);
						if (!empty($tags)) {
							$legend .= $this->render('_tags', [
								'models' => $tags,
							]);
						}
					}
					if ($usersLinks) {
						$legend = Html::a($legend, ['/user/customer/view', 'id' => $issueUser->user_id]);
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
			<?= GridView::widget([
				'dataProvider' => new ActiveDataProvider([
					'query' => $model->getLinkedIssues()
						->with('customer')
						->with('tags'),
				]),
				'summary' => '',
				'caption' => Yii::t('issue', 'Linked'),
				'emptyText' => '',
				'showOnEmpty' => false,
				'columns' => [
					[
						'label' => Yii::t('issue', 'Issue'),
						'format' => 'html',
						'value' => function (IssueInterface $issue): string {
							return Html::a(
									Html::encode($issue->getIssueName()), ['issue/view', 'id' => $issue->getIssueId()]) . $this->render('_tags', ['models' => IssueTag::typeFilter($issue->tags)]);
						},
					],
					[
						'label' => Yii::t('issue', 'Type'),
						'attribute' => 'typeName',
					],
					[
						'label' => Yii::t('issue', 'Stage'),
						'attribute' => 'stageName',
					],
					[
						'label' => Yii::t('issue', 'Customer'),
						'format' => 'html',
						'value' => function (IssueInterface $issue): string {
							return Html::encode($issue->getIssueModel()->customer->getFullName()) . $this->render('_tags', ['models' => IssueTag::typeFilter($issue->tags, IssueTag::TYPE_CLIENT)]);
						},
						'attribute' => 'customer',
					],
					[
						'class' => ActionColumn::class,
						'controller' => '/issue/relation',
						'template' => '{delete}',
						'visible' => $relationActionColumn,
						'urlCreator' => static function (string $action, IssueInterface $issue) use ($model): string {
							return Url::to(['/issue/relation/delete', 'id' => $model->getIssueRelationId($issue->getIssueId()), 'returnUrl' => Url::current()]);
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
				'legend' => Yii::t('common', 'Issue details') . $this->render('_tags', ['models' => IssueTag::typeFilter($model->tags)]),
				'legendOptions' => [
					'encode' => false,
				],
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
						],
						[
							'attribute' => 'archives_nr',
							'visible' => $model->isArchived(),
						],
						[
							'attribute' => 'type',
							'label' => $model->getAttributeLabel('type_id'),
						],
						[
							'attribute' => 'stage',
							'label' => $model->getAttributeLabel('stage_id'),
							'value' => function (IssueInterface $issue): string {
								if (!empty($issue->getIssueModel()->stage_change_at)) {
									return Yii::t('issue', '{stage} ({from})', [
										'stage' => $issue->getIssueStage()->name,
										'from' => Yii::$app->formatter->asDate($issue->getIssueModel()->stage_change_at),
									]);
								}
								return $issue->getIssueStage()->name;
							},
						],
						[
							'attribute' => 'stage_deadline_at',
							'label' => $model->getAttributeLabel('stage_deadline_at'),
							'format' => 'date',
							'visible' => !empty($model->getIssueModel()->stage_deadline_at),
						],
						[
							'attribute' => 'entityResponsible',
							'label' => $model->getAttributeLabel('entity_responsible_id'),
						],
						'created_at:date',
						'updated_at:date',
						'signing_at:date',
						[
							'attribute' => 'type_additional_date_at',
							'format' => 'date',
							'visible' => $model->type->with_additional_date,
						],
						'details:ntext',
					],

				],
			]) ?>


		</div>
	</div>


</div>

