<?php

use common\modules\court\models\Court;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\CustomerIssuesDataColumn;
use common\widgets\grid\IssuesDataColumn;
use common\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var Court $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('court', 'Courts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

?>
<div class="court-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('court', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
	</p>


	<div class="row">
		<div class="col-md-6">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [

					'phone:ntext',
					'fax',
					'email:email',
					[
						'label' => Yii::t('court', 'Address'),
						'format' => 'html',
						'visible' => !empty($model->addresses),
						'value' => function () use ($model): ?string {
							if (empty($model->addresses)) {
								return null;
							}
							$addressItems = [];
							foreach ($model->addresses as $address) {
								$addressItems[] = Yii::$app->formatter->asAddress($address);
							}
							if (count($addressItems) === 1) {
								return reset($addressItems);
							}
							return Html::ul($addressItems, [
								'encode' => false,
							]);
						},
					],
					[
						'attribute' => 'parent_id',
						'format' => 'html',
						'visible' => !empty($model->parent),
						'value' => $model->parent
							? Html::a(
								Html::tag('strong', Html::encode($model->parent->name)),
								['view', 'id' => $model->parent_id]
							)
							: null,
					],
				],
			]) ?>
		</div>

		<div class="col-md-6">
			<?= GridView::widget([
				'summary' => false,
				'caption' => Yii::t('court', 'Childes'),
				'showOnEmpty' => false,
				'emptyText' => false,
				'dataProvider' => new ActiveDataProvider([
					'query' => $model->getChildes(),
				]),
				'columns' => [
					[
						'attribute' => 'name',
						'format' => 'html',
						'noWrap' => true,
						'value' => function (Court $child): string {
							return Html::a($child->name, ['view', 'id' => $child->id]);
						},
					],
					'phone',
					'email:email',
				],
			]) ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<?= GridView::widget([
				'summary' => false,
				'caption' => Yii::t('court', 'Lawsuits'),
				'showOnEmpty' => false,
				'emptyText' => false,
				'dataProvider' => new ActiveDataProvider([
					'query' => $model->getLawsuits()
						->with('issues')
						->with('issues.customer.userProfile')
						->orderBy(['due_at' => SORT_ASC]),
				]),
				'columns' => [
					[
						'class' => IssuesDataColumn::class,
					],
					[
						'class' => CustomerIssuesDataColumn::class,
					],
					'signature_act',
					'due_at:datetime',
					'locationName',
					'presenceOfTheClaimantName',
					[
						'class' => ActionColumn::class,
						'controller' => '/court/lawsuit',
					],
				],
			]) ?>

		</div>

	</div>


</div>
