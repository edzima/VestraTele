<?php

use common\modules\lead\models\entities\LeadMarketOptions;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model LeadMarketOptions */

?>

<div class="options-form-fields-wrapper">

	<?= $form->field($model, 'visibleArea')->dropDownList(LeadMarketOptions::visibleAreaNames()) ?>

	<?= $form->field($model, 'visibleAddressDetails')->checkbox() ?>


</div>
