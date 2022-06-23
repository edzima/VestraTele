<?php

use common\modules\lead\models\entities\LeadMarketOptions;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model LeadMarketOptions */

?>

<div class="options-form-fields-wrapper">

	<?= $form->field($model, 'visibleRegion')->checkbox() ?>

	<?= $form->field($model, 'visibleDistrict')->checkbox() ?>

	<?= $form->field($model, 'visibleCommune')->checkbox() ?>

	<?= $form->field($model, 'visibleCity')->checkbox() ?>

	<?= $form->field($model, 'visibleAddressDetails')->checkbox() ?>


</div>
