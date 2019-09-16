<?php

use frontend\models\BenefitAmountAlignmentForm;
use yii\data\ArrayDataProvider;
use yii\web\View;

/* @var $this View */
/* @var $model BenefitAmountAlignmentForm */
/* @var $dataProvider ArrayDataProvider */
?>


<?= $this->render('_form', ['model' => $model]) ?>

<?php if ($dataProvider !== null): ?>
	<?= $this->render('details', [
		'model' => $model,
		'dataProvider' => $dataProvider,
	]) ?>
<?php endif; ?>

