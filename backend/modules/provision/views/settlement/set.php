<?php

use backend\modules\provision\models\SettlementProvisionsForm;
use yii\web\View;

/* @var $this View */
/* @var $model SettlementProvisionsForm */

$this->title = Yii::t('backend', 'Set provisions for settlement');

?>
<div class="provision-settlement-set">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
