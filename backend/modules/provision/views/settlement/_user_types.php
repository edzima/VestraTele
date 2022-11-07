<?php

use backend\helpers\Html;
use backend\helpers\Url;
use backend\modules\provision\models\SettlementUserProvisionsForm;
use backend\widgets\GridView;
use common\models\provision\IssueProvisionType;
use common\models\provision\ProvisionUser;
use common\widgets\grid\ActionColumn;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\View;

/* @var $this View */
/* @var $model SettlementUserProvisionsForm */
?>

<div class="provision-settlement-user-type-grid col-md-4 col-lg-4">

	<?= GridView::widget([
		'dataProvider' => new ArrayDataProvider([
			'allModels' => $model->getTypes(),
		]),
		'toolbar' => Html::a(
			Html::icon('plus'),
			['type/create-settlement', 'id' => $model->getModel()->id, 'issueUserType' => $model->getIssueUser()->type], [
			'class' => 'btn btn-warning btn-sm',
			'title' => Yii::t('provision', 'Create provision type'),

		]),
		'panel' => [
			'heading' => $model->getIssueUser()->getTypeWithUser(),
			'after' => false,
			'footer' => false,
		],
		'summary' => false,
		'columns' => [
			[
				'attribute' => 'name',
				'label' => Yii::t('provision', 'Name'),
				'format' => 'html',
				'value' => static function (IssueProvisionType $type): string {
					return Html::tag('strong', '( ' . $type->getTypeName() . ' ) ') . Html::encode($type->name);
				},
			],
			[
				'label' => Yii::t('provision', 'Value'),
				'value' => static function (IssueProvisionType $type) use ($model): ?string {
					$data = $model->getData();
					$data->type = $type;
					$selfies = $data->getSelfQuery()->all();
					if (empty($selfies) && $type->getBaseType() !== null) {
						$data->type = $type->getBaseType();
						$selfies = $data->getSelfQuery()->all();
					}
					if (empty($selfies)) {
						return null;
					}
					if (count($selfies) === 1) {
						$self = reset($selfies);
						if ($type->getBaseType() !== null) {
							return ProvisionUser::createFromBaseType($self, $type)->getFormattedValue();
						}
						return $self->getFormattedValue();
					}
					$values = ArrayHelper::getColumn($selfies, 'formattedValue');
					return implode(', ', $values);
				},
			],
			[
				'class' => ActionColumn::class,
				'template' => '{generate} {view}',
				'buttons' => [
					'generate' => static function (string $url, IssueProvisionType $type) use ($model): string {
						return
							$model->getData()->hasSelfies() ?
								Html::a(
									Html::icon('piggy-bank'),
									[
										'user',
										'id' => $model->getModel()->id,
										'issueUserType' => $type->getIssueUserType(),
										'typeId' => $type->id,
									]
								) : '';
					},
					'view' => static function (string $url, IssueProvisionType $type) use ($model): string {
						return Html::a(
							Html::icon('eye-open'),
							Url::userProvisions($model->getIssueUser()->user_id, $type->id)
						);
					},
				],
			],
		],
	]);
	?>

</div>

