<?php

use common\models\issue\Issue;
use common\models\issue\IssueUser;
use common\models\user\User;
use common\modules\issue\widgets\IssueUsersWidget;
use common\widgets\FieldsetDetailView;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model Issue */

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

<fieldset>
	<legend>
		<h1 class="inline"><?= Html::encode($this->title) ?></h1>
		<button class="btn toggle pull-right" data-toggle="#issue-details">
			<i class="glyphicon glyphicon-chevron-down"></i>
		</button>
	</legend>
	<div id="issue-details">
		<div class="row">
			<div class="col-md-6">
				<?= IssueUsersWidget::widget([
					'model' => $model,
					'type' => IssueUsersWidget::TYPE_CUSTOMERS,
					'legendEncode' => false,
					'legend' => static function (IssueUser $issueUser): string {
						$legend = Html::a($issueUser->getTypeName()
							. ' - '
							. Html::encode($issueUser->user->getFullName()),
							['/user/customer/view', 'id' => $issueUser->user_id], [
								'target' => '_blank',
							]);
						if ($issueUser->type === IssueUser::TYPE_CUSTOMER) {
							return $legend;
						}
						$legend .= Html::beginTag('span', ['class' => 'pull-right']);
						$legend .= Html::a(Html::icon('pencil'),
							[
								'/issue/user/update-type',
								'issueId' => $issueUser->issue_id,
								'userId' => $issueUser->user_id,
								'type' => $issueUser->type,
							]);
						if (Yii::$app->user->can(User::ROLE_ADMINISTRATOR)) {
							$legend .= Html::a(Html::icon('trash'),
								[
									'/issue/user/delete',
									'issueId' => $issueUser->issue_id,
									'userId' => $issueUser->user_id,
									'type' => $issueUser->type,
								], [
									'data-method' => 'POST',
									'data-confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
								]);
						}
						$legend .= Html::endTag('span');

						return $legend;
					},
					'withAddress' => static function (IssueUser $issueUser): bool {
						return $issueUser->type === IssueUser::TYPE_CUSTOMER;
					},
				]) ?>
				<?= IssueUsersWidget::widget([
					'model' => $model,
					'type' => IssueUsersWidget::TYPE_WORKERS,
					'legendEncode' => false,
					'legend' => static function (IssueUser $issueUser): string {
						return Html::a($issueUser->getTypeName()
							. ' - '
							. Html::encode($issueUser->user->getFullName()),
							['/user/worker/view', 'id' => $issueUser->user_id], [
								'target' => '_blank',
							]);
					},
				]) ?>
			</div>
			<div class="col-md-6">
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
							'victimFullname:text:Poszkodowany',
							[
								'attribute' => 'type',
								'label' => $model->getAttributeLabel('type_id'),
							],
							[
								'attribute' => 'stage',
								'label' => $model->getAttributeLabel('stage_id'),
							],
							[
								'attribute' => 'entityResponsible',
								'label' => $model->getAttributeLabel('entity_responsible_id'),
							],
							'created_at:date',
							'updated_at:date',
							'signing_at:date',
							[
								'attribute' => 'accident_at',
								'format' => 'date',
								'visible' => $model->isAccident(),
							],
							'details:ntext',
						],

					],
				]) ?>
			</div>
		</div>


	</div>

</fieldset>

