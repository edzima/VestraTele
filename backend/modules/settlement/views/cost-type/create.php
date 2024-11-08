<?php

use backend\modules\settlement\models\CostTypeForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var CostTypeForm $model */

$this->title = Yii::t('settlement', 'Create Cost Type');
$this->params['breadcrumbs'][] = ['url' => ['cost/index'], 'label' => Yii::t('settlement', 'Costs')];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Cost Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cost-type-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
