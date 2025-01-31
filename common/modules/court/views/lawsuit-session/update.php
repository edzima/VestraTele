<?php

use common\modules\court\models\LawsuitSessionForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var LawsuitSessionForm $model */

$this->title = Yii::t('court', 'Update Lawsuit Session: {signature} - {date}', [
	'signature' => $model->getLawsuit()->signature_act,
	'date' => Yii::$app->formatter->asDate($model->date_at),
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('court', 'Lawsuits'), 'url' => ['lawsuit/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getLawsuit()->signature_act, 'url' => ['lawsuit/view', 'id' => $model->lawsuit_id]];
$this->params['breadcrumbs'][] = Yii::t('court', 'Update');
?>
<div class="lawsuit-session-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
