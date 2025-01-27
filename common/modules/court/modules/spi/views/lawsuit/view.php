<?php

use common\helpers\Url;
use common\modules\court\modules\spi\entity\lawsuit\LawsuitDetailsDto;
use common\modules\court\modules\spi\Module;
use kartik\tabs\TabsX;
use yii\web\View;
use yii\widgets\DetailView;

/** @var View $this */
/** @var LawsuitDetailsDto $model */

$this->title = Module::t('lawsuit', 'Lawsuit: {signature}', [
	'signature' => $model->signature,
]);
$this->params['breadcrumbs'][] = [
	'url' => ['index'],
	'label' => Module::t('lawsuit', 'Lawsuits'),
];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="spi-lawsuit-view">

	<?= TabsX::widget([
		'items' => [
			[
				'label' => Module::t('document', 'Documents'),
				'linkOptions' => [
					'data-url' => Url::to([
						'document/lawsuit',
						'id' => $model->id,
						'appeal' => $this->params['appeal'],
					]),
				],
			],
		],
	]) ?>

	<?= DetailView::widget([
		'model' => $model,
	])
	?>

</div>



