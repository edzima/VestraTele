<?php

use backend\models\UserForm;
use common\models\UserProfile;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use common\models\User;
use vova07\fileapi\Widget;
use yii\data\ActiveDataProvider;
use yii\rbac\Permission;
use yii\rbac\Role;

/* @var $this yii\web\View */
/* @var $profile UserProfile */
/* @var $user UserForm */
/* @var $form ActiveForm */
/* @var $roles Role[] */
/* @var $permissions Permission[] */

$this->title = Yii::t('backend', 'Update user: {username}', ['username' => $user->username]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="user-update">

	<?php $form = ActiveForm::begin() ?>

	<?= $form->field($user, 'username')->textInput(['maxlength' => true]) ?>

	<?= $form->field($user, 'password')->passwordInput(['maxlength' => true]) ?>

	<?= $form->field($user, 'email')->textInput(['maxlength' => true]) ?>

	<?= $form->field($user, 'status')->label(Yii::t('backend', 'Status'))->radioList(User::statuses()) ?>

	<?= $form->field($user, 'roles')->checkboxList($roles) ?>

	<?= $form->field($profile, 'firstname')->textInput(['maxlength' => true]) ?>

	<?= $form->field($profile, 'lastname')->textInput(['maxlength' => true]) ?>

	<?= $form->field($profile, 'phone')->textInput(['maxlength' => true]) ?>

	<?= $form->field($profile, 'avatar_path')->widget(
		Widget::className(),
		[
			'settings' => [
				'url' => ['/site/fileapi-upload'],
			],
			'crop' => true,
			'cropResizeWidth' => 100,
			'cropResizeHeight' => 100,
		]
	) ?>

	<?= $form->field($user, 'parent_id')
		->widget(Select2::class, [
				'data' => $user->getParents(),
				'options' => [
					'placeholder' => 'Przełożony',
				],
				'pluginOptions' => [
					'allowClear' => true,
				],
			]
		) ?>

	<?php

	if (!empty($user->getModel()->getParentsIds())) {
		$dataProvider = new ActiveDataProvider([
			'query' => $user->getModel()->getParentsQuery()->with(['userProfile']),
			'pagination' => false,
		]);

		echo $this->render('_users', [
			'legend' => 'Przełożeni',
			'dataProvider' => $dataProvider,
		]);
	}

	if (!empty($user->getModel()->getAllChildesIds())) {
		$dataProvider = new ActiveDataProvider([
			'query' => $user->getModel()->getAllChildesQuery()->with(['userProfile']),
			'pagination' => false,
		]);
		echo $this->render('_users', [
			'legend' => 'Podopieczni',
			'dataProvider' => $dataProvider,
		]);
	}

	?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Update'), ['class' => 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end() ?>

</div>
