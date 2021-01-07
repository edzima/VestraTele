<?php

use common\models\provision\ProvisionTypeSearch;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel ProvisionTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Provisions types');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Provisions'), 'url' => ['/provision/provision']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provision-type-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('backend', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>


	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'name',
			'value',
			'is_percentage:boolean',
			'only_with_tele:boolean',
			'is_default:boolean',
			'calculationTypesNames',
			'rolesNames',
			'issueTypesNames',
			//'date_from:date',
			//'date_to:date',

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
