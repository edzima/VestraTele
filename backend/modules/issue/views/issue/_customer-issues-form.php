<?php

use backend\helpers\Html;
use backend\helpers\Url;
use backend\modules\issue\models\IssueForm;
use common\models\issue\IssueInterface;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $baseIssue IssueInterface|null */
/* @var $model IssueForm */
?>

<div class="from-issue-form">

	<div class="row">
		<?= Html::beginForm(['create', 'customerId' => $model->getCustomer()->id], 'GET') ?>

		<div class="col-md-5">


			<?= Select2::widget([
				'name' => 'issueId',
				'value' => $baseIssue ? $baseIssue->getIssueId() : null,
				'data' => $baseIssue
					? [
						$baseIssue->getIssueId() => $baseIssue->getIssueName() . ' - ' . $baseIssue->getIssueModel()->customer->getFullName(),
					]
					: []
				,
				'options' => ['placeholder' => Yii::t('backend', 'Find Customer Issue and copy from them ...')],
				'addon' => [
					'prepend' => [
						'content' => '<i class="fa fa-suitcase"></i>',
					],
					'append' => [
						'content' => Html::submitButton('<i class="fa fa-download"></i>', [
							'class' => 'btn btn-primary',
							'title' => Yii::t('backend', 'Copy from Issue'),
							'aria-label' => Yii::t('backend', 'Copy from Issue'),
						]),
						'asButton' => true,
					],
				],
				'pluginOptions' => [
					'allowClear' => true,
					'minimumInputLength' => 3,
					'ajax' => [
						'url' => Url::to(['user/customer-issues']),
						'dataType' => 'json',
					],
				],
			]) ?>

		</div>

		<?= Html::endForm() ?>
	</div>
	<br>
</div>
