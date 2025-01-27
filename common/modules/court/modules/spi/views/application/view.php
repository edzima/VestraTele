<?php

use common\modules\court\modules\spi\entity\application\ApplicationViewDTO;
use common\modules\court\modules\spi\Module;
use yii\web\View;
use yii\widgets\DetailView;

/** @var View $this */
/** @var ApplicationViewDTO $model */

$this->title = Module::t('application', 'Application: {signature}', [
	'signature' => $model->signature,
]);
$this->params['breadcrumbs'][] = [
	'url' => ['index'],
	'label' => Module::t('application', 'Applications'),
];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="spi-application-view">

	<?= DetailView::widget([
		'model' => $model,
	])
	?>

</div>



