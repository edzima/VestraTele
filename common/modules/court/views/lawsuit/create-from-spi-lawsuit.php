<?php

use common\modules\court\models\LawsuitIssueForm;
use common\modules\court\modules\spi\entity\lawsuit\LawsuitDetailsDto;
use common\widgets\GridView;
use yii\data\DataProviderInterface;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var LawsuitIssueForm $model */
/** @var LawsuitDetailsDto $lawsuit */
/** @var DataProviderInterface $partiesDataProvider */

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
		'issue' => null,
	]) ?>

	<?= GridView::widget([
		'caption' => Yii::t('court', 'Parties'),
		'dataProvider' => $partiesDataProvider,
	]) ?>

	<?= $this->render('_spi_lawsuit_view', [
		'model' => $lawsuit,
	]) ?>

</div>
