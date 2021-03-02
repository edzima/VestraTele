<?php

use backend\helpers\Html;
use backend\helpers\Url;
use backend\modules\provision\models\SettlementProvisionsForm;
use common\models\provision\ProvisionType;
use common\models\provision\ProvisionUser;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model SettlementProvisionsForm */
/* @var $form ActiveForm */
?>

<div class="settlement-provision-users-form">


	<?php $form = ActiveForm::begin([
			'id' => 'provision-type-form',
		]
	); ?>

	<div class="row">
		<?php foreach ($model->getIssue()->users as $issueUser) {
			$options = $model->getUserTypes($issueUser->type);
			if (!empty($options)) {
				echo $form->field($model, $model->getIssueUserAttributeName($issueUser->type), [
					'options' => [
						'class' => 'col-md-4',
					],
				])->radioList($options, [
					'item' => function ($index, ProvisionType $type, $name, $checked, $value) use ($issueUser): string {
						$itemOptions = [];
						/** @var ProvisionUser[] $selfies */
						$selfies = $type->getProvisionUsers()
							->onlySelf($issueUser->user_id)
							->all();
						if (empty($selfies)) {
							$label = $type->name . Html::a(
									Html::icon('pencil'),
									['/provision/user/user', 'id' => $issueUser->user_id, 'typesIds' => [$type->id]]);
							return Html::radio($name, $checked, array_merge([
								'value' => $value,
								'label' => $label,
							], $itemOptions));
						}
						$list = [];
						foreach ($selfies as $selfy) {
							$label = $selfy->getTypeWithValue() . Html::a(
									Html::icon('pencil'),
									['/provision/user/user', 'id' => $issueUser->user_id, 'typesIds' => [$type->id]]);
							$list[] = Html::radio($name, $checked, array_merge([
								'value' => $value,
								'label' => $label,
							], $itemOptions));
						}
						return implode("\n", $list);
					},
				])
					->label($issueUser->getTypeName() . ' - ' . Html::a($issueUser->user, Url::userProvisions($issueUser->user_id), ['target' => '_blank']));
			}
		} ?>

	</div>

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


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

