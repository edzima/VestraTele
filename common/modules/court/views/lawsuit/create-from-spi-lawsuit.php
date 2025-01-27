<?php

use common\modules\court\models\LawsuitIssueForm;
use common\modules\court\modules\spi\entity\lawsuit\LawsuitDetailsDto;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var LawsuitIssueForm $model */
/** @var LawsuitDetailsDto $lawsuit */

$this->title = Yii::t('court', 'Create Lawsuit: {signature} - {courtName}', [
	'signature' => $lawsuit->signature,
	'courtName' => $model->getCourtName(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('court', 'Lawsuits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lawsuit-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
