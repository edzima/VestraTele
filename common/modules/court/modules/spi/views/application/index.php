<?php

use common\modules\court\modules\spi\entity\search\ApplicationSearch;
use common\modules\court\modules\spi\Module;
use common\widgets\GridView;
use yii\data\DataProviderInterface;
use yii\web\View;

/** @var View $this */
/** @var ApplicationSearch $searchModel */
/** @var DataProviderInterface $dataProvider */

$this->title = Module::t('application', 'Applications');
$this->params['breadcrumbs'][] = $this->title;

$dataProvider->pagination->totalCount = $dataProvider->getTotalCount();
?>

<div class="spi-application-index">

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		//		'columns' => [
		//			'signature',
		//			'subject',
		//			'value',
		//			'result',
		//			'receiptDate:date',
		//			'lastUpdate:date',
		//			'repertoryName',
		//			'departmentName',
		//			'courtName',
		//			//	'partyName',
		//			[
		//				'class' => ActionColumn::class,
		//				'template' => '{view}',
		//			],
		//		],
	])
	?>
</div>



