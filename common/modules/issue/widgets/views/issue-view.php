<?php

use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\issue\IssueUser;
use common\models\user\User;
use common\modules\issue\widgets\IssueUsersWidget;
use common\widgets\FieldsetDetailView;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model Issue */
/* @var $usersLinks bool */
/* @var $userMailVisibilityCheck bool */

$provision = $model->getProvision();
if ($provision) {
	$details = [];
	$details[] = 'PROWIZJA - rodzaj: ' . $provision->getTypeName();
	$details[] = 'Podstawa: ' . $provision->getBase();
	$details[] = 'Procent\krotność: ' . $provision->getValue();
	$details[] = $model->details;
	$model->details = implode("\n", $details);
}

?>

<div id="issue-details">
	<div class="row">
		<div class="col-md-8 col-lg-7">
			<?= IssueUsersWidget::widget([
				'model' => $model,
				'type' => IssueUsersWidget::TYPE_CUSTOMERS,
				'legendEncode' => !$usersLinks,
				'withCheckEmailVisibility' => $userMailVisibilityCheck,
				'legend' => static function (IssueUser $issueUser) use ($usersLinks): string {
					$legend = $issueUser->getTypeWithUser();
					if ($usersLinks) {
						$legend = Html::a($legend, ['/user/customer/view', 'id' => $issueUser->user_id], [
							'target' => '_blank',
						]);
					}
					return $legend;
				},
				'afterLegend' => static function (IssueUser $issueUser) use ($usersLinks): string {
					if ($issueUser->type === IssueUser::TYPE_CUSTOMER || !$usersLinks) {
						return '';
					}
					$content = Html::beginTag('span', ['class' => 'pull-right form-group']);
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
					if (Yii::$app->user->can(User::ROLE_ADMINISTRATOR)) {
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
			<?= IssueUsersWidget::widget([
				'model' => $model,
				'type' => IssueUsersWidget::TYPE_WORKERS,
				'legendEncode' => !$usersLinks,
				'withCheckEmailVisibility' => $userMailVisibilityCheck,
				'legend' => static function (IssueUser $issueUser) use ($usersLinks): string {
					$legend = $issueUser->getTypeWithUser();
					if ($usersLinks) {
						$legend = Html::a($legend,
							['/user/worker/view', 'id' => $issueUser->user_id], [
								'target' => '_blank',
							]);
					}
					return $legend;
				},
				'afterLegend' => static function (IssueUser $issueUser) use ($usersLinks): string {
					if (!$usersLinks) {
						return '';
					}
					$content = Html::beginTag('span', ['class' => 'pull-right form-group']);
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
					$content .= Html::endTag('span');

					return $content;
				},
			]) ?>
		</div>
		<div class="col-md-4 col-lg-5">
			<?= FieldsetDetailView::widget([
				'legend' => Yii::t('common', 'Issue details'),
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

