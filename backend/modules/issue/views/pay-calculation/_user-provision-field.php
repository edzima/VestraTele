<?php

use backend\helpers\Url;
use backend\modules\issue\controllers\PayCalculationController;
use backend\modules\issue\models\IssueProvisionUsersForm;
use common\models\User;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model IssueProvisionUsersForm */
/* @var $form ActiveForm */
/* @var $attribute string */
/* @var $user User */
/* @var $options string[] */

if (empty($options)) {
	PayCalculationController::addUserProvisionFlash($user);
}
?>


<?=
$form->field($model, $attribute, [
	'options' => [
		'class' => 'col-md-4',
	],
])->dropDownList($options)
	->hint(Html::a($user, Url::userProvisions($user->id), ['target' => '_blank'])) ?>


