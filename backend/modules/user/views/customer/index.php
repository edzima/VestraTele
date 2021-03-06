<?php

use backend\helpers\Url;
use backend\modules\user\models\search\CustomerUserSearch;
use backend\widgets\GridView;
use common\models\user\Customer;
use common\models\user\UserProfile;
use kartik\grid\ActionColumn;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $searchModel CustomerUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Customers');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="customer-user-index">

	<p>
		<?= Html::a(Yii::t('backend', 'Create customer'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'attribute' => 'firstname',
				'value' => 'profile.firstname',
				'label' => UserProfile::instance()->getAttributeLabel('firstname'),
			],
			[
				'attribute' => 'lastname',
				'value' => 'profile.lastname',
				'label' => UserProfile::instance()->getAttributeLabel('lastname'),
			],
			[
				'attribute' => 'city',
				'value' => 'homeAddress.city.name',
				'label' => Yii::t('common', 'City'),
			],
			[
				'attribute' => 'street',
				'value' => 'homeAddress.info',
				'label' => Yii::t('common', 'Street'),
			],
			'email:email',
			[
				'attribute' => 'phone',
				'value' => 'profile.phone',
				'label' => Yii::t('common', 'Phone number'),
			],
			'updated_at:datetime',
			[
				'class' => ActionColumn::class,
				'template' => '{create-issue} {view} {update}',
				'buttons' => [
					'create-issue' => static function (string $url, Customer $model): string {
						return Html::a(
							'<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
							Url::toRoute(['/issue/issue/create', 'customerId' => $model->id]),
							[
								'title' => Yii::t('backend', 'Create issue'),
								'aria-label' => Yii::t('backend', 'Create issue'),
							]);
					},
				],
			],
		],
	]) ?>

</div>
