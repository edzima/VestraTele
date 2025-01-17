<?php

use common\modules\court\modules\spi\models\search\LawsuitSearch;
use common\modules\court\modules\spi\Module;
use common\modules\court\modules\spi\widgets\AppealsNavWidget;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\data\DataProviderInterface;
use yii\web\View;

/** @var View $this */
/** @var LawsuitSearch $searchModel */
/** @var DataProviderInterface $dataProvider */

$this->title = Module::t('lawsuit', 'Lawsuits');
$this->params['breadcrumbs'][] = $this->title;

//echo Html::dump([
//	'page' => $dataProvider->getPagination(),
//	'sort' => $dataProvider->getSort(),
//]);
?>

<?= AppealsNavWidget::widget() ?>

<div class="spi-lawsuit-index">

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			'signature',
			'subject',
			'value',
			'result',
			'receiptDate:date',
			'lastUpdate:date',
			'repertoryName',
			'departmentName',
			'courtName',
			//	'partyName',
			[
				'class' => ActionColumn::class,
				'template' => '{view}',
			],
		],
	])
	?>
</div>



