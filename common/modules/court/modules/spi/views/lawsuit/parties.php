<?php

use common\helpers\Html;
use common\modules\court\modules\spi\entity\lawsuit\LawsuitPartyDTO;
use common\widgets\GridView;
use yii\data\DataProviderInterface;

/** @see LawsuitPartyDTO */
/** @var DataProviderInterface $dataProvider */

?>

<div class="lawsuit-parties">
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'id' => 'lawsuit-parties-grid',
		'summary' => false,
		'pjax' => true,
		'pjaxSettings' => [
			'neverTimeout' => true,
			'refreshGrid' => true,
		],
		'columns' => [
			'role',
			'name',
			'address',
			'createdDate:datetime',
			'modificationDate:datetime',
			[
				'attribute' => 'representatives',
				'format' => 'html',
				'label' => '',
				'value' => function (LawsuitPartyDTO $model): ?string {
					$representatives = $model->getRepresentatives();
					if (empty($representatives)) {
						return null;
					}
					$content = [];
					foreach ($representatives as $representative) {
						$content[] = $representative->role . ' - ' . $representative->name;
					}
					return Html::ul($content);
				},
			],
		],
	]) ?>
</div>
