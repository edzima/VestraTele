<?php

use backend\modules\user\models\WorkerUserForm;
use backend\modules\user\widgets\UserProfileFormWidget;
use common\widgets\address\AddressFormWidget;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model WorkerUserForm */
/* @var $form ActiveForm */

$this->title = Yii::t('backend', 'Update customer: {username}', ['username' => $model->username]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="user-update">

	<?php $form = ActiveForm::begin() ?>

	<?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

	<?= UserProfileFormWidget::widget([
		'model' => $model->getProfile(),
		'form' => $form,
	]) ?>


	<?= AddressFormWidget::widget([
		'form' => $form,
		'model' => $model->getAddress(),
	]) ?>


	<?= $form->field($model, 'status')->label(Yii::t('backend', 'Status'))->radioList($model::getStatusNames()) ?>

	<?= $form->field($model, 'roles')->checkboxList($model::getRolesNames()) ?>


	<?= $form->field($model, 'parent_id')
		->widget(Select2::class, [
				'data' => $model->getParents(),
				'options' => [
					'placeholder' => 'Przełożony',
				],
				'pluginOptions' => [
					'allowClear' => true,
				],
			]
		) ?>

	<?php

	if (!empty($model->getModel()->getParentsIds())) {
		$dataProvider = new ActiveDataProvider([
			'query' => $model->getModel()->getParentsQuery()->with(['userProfile']),
			'pagination' => false,
		]);

		echo $this->render('_users', [
			'legend' => 'Przełożeni',
			'dataProvider' => $dataProvider,
		]);
	}

	if (!empty($model->getModel()->getAllChildesIds())) {
		$dataProvider = new ActiveDataProvider([
			'query' => $model->getModel()->getAllChildesQuery()->with(['userProfile']),
			'pagination' => false,
		]);
		echo $this->render('_users', [
			'legend' => 'Podopieczni',
			'dataProvider' => $dataProvider,
		]);
	}

	?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end() ?>

</div>
