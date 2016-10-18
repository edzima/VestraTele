<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use common\models\User;
use common\models\UserProfile;
use trntv\yii\datetime\DateTimeWidget;
use vova07\fileapi\Widget;

/* @var $this yii\web\View */
/* @var $profile common\models\UserProfile */
/* @var $user backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */

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


	<?= $form->field($user, 'typ_work')->dropDownList(['prompt'=> 'Wybierz typ pracownika', 'T'=>'Telemarketer', 'P'=>'Przedstawiciel']) ?>															

	
    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Update'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end() ?>

</div>
