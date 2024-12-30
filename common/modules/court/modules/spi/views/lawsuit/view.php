<?php

use common\modules\court\modules\spi\models\lawsuit\LawsuitDetailsDto;
use common\modules\court\modules\spi\Module;
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

	<?= DetailView::widget([
		'model' => $model,
	])
	?>

</div>



