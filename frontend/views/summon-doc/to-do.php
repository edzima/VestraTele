<?php

use common\models\issue\search\SummonDocLinkSearch;
use common\models\issue\SummonDocLink;
use common\widgets\grid\CustomerDataColumn;
use frontend\helpers\Html;
use frontend\widgets\IssueColumn;

/* @var $this yii\web\View */
/* @var $searchModel SummonDocLinkSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('issue', 'Summon Docs');

?>

<div class="summon-doc-to-do">

	<h1><?= \frontend\helpers\Html::encode($this->title) ?></h1>

	<?= \frontend\widgets\GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'class' => IssueColumn::class,
			],
			[
				'class' => CustomerDataColumn::class,
				'attribute' => 'customerName',
			],
			[
				'attribute' => 'doc_type_id',
				'value' => 'doc.name',
				'label' => Yii::t('issue', 'Doc Name'),
				'filter' => $searchModel->getDocsNames(),
			],
			[
				'attribute' => 'summonTypeId',
				'label' => Yii::t('issue', 'Summon Type'),
				'value' => function (SummonDocLink $docLink): string {
					return Html::a($docLink->summon->getTypeName(), [
						'summon/view', 'id' => $docLink->summon_id,
					]);
				},
				'format' => 'html',
				'filter' => $searchModel->getSummonTypesNames(),
			],

		],
	]) ?>

</div>
