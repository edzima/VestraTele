<?php

use backend\helpers\Url;
use backend\modules\issue\models\IssueProvisionUsersForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model IssueProvisionUsersForm */
/* @var $form ActiveForm */
?>

<div class="issue-provision-users-form">

	<h3>Prowizje</h3>


	<div class="row">

		<?= $form->field($model, 'lawyerProvision', [
			'options' => [
				'class' => 'col-md-4',
			],
		])->dropDownList($model->getLawyerOptions())
			->hint(Html::a($model->getLawyer(), Url::userProvisions($model->getLawyer()->id), ['target' => '_blank']))
		?>

		<?= $form->field($model, 'agentProvision', [
			'options' => [
				'class' => 'col-md-4',
			],

		])->dropDownList($model->getAgentOptions())
			->hint(Html::a($model->getAgent(), Url::userProvisions($model->getAgent()->id), ['target' => '_blank']))
		?>
		<?php if ($model->isWithTele()): ?>
			<?= $form->field($model, 'teleProvision', [
				'options' => [
					'class' => 'col-md-4',
				],
			])->dropDownList($model->getTeleOptions())
				->hint(Html::a($model->getTele(), Url::userProvisions($model->getTele()->id), ['target' => '_blank']))

			?>

		<?php endif; ?>


	</div>

</div>
