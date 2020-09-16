<?php

use backend\modules\provision\models\ProvisionUserForm;
use common\models\provision\ProvisionUser;
use common\models\user\Worker;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $title string */
/* @var $models ProvisionUser[] */
/* @var $formModel ProvisionUserForm */
/* @var $form ActiveForm */
?>

<?php

if (!empty($models)): ?>
	<fieldset>
		<legend><?= Html::encode($title) ?></legend>
		<div class="forms-wrapper row">
			<?php foreach ($models as $index => $provisionUser): ?>
				<div class="col-md-3">
					<h2>
						<?php if ($provisionUser->from_user_id === $formModel->getUser()->id && $provisionUser->to_user_id === $formModel->getUser()->id): ?>
						<?php elseif ($provisionUser->from_user_id === $formModel->getUser()->id): ?>
							Dla <?= Html::a(
								Worker::userName($provisionUser->to_user_id),
								['user/user', 'id' => $provisionUser->to_user_id], [
								'target' => '_blank',
							]) ?>
						<?php else: ?>
							Od <?= Html::a(
								Worker::userName($provisionUser->from_user_id),
								['user/user', 'id' => $provisionUser->from_user_id], [
								'target' => '_blank',
							]) ?>
						<?php endif; ?>
					</h2>
					<?= $this->render('_form_model', [
						'form' => $form,
						'formModel' => $formModel,
						'model' => $provisionUser,
						'index' => $index,
					]) ?>
				</div>
			<?php endforeach; ?>
		</div>
	</fieldset>
<?php endif; ?>
