<?php

namespace backend\modules\settlement\widgets;

use backend\widgets\IssueColumn;
use common\models\issue\IssuePayCalculation;
use common\models\user\User;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\IssuePayCalculationGrid as BaseIssuePayCalculationGrid;
use Yii;
use yii\bootstrap\Html;

class IssuePayCalculationGrid extends BaseIssuePayCalculationGrid {

	protected const ISSUE_COLUMN = IssueColumn::class;

	protected function actionColumn(): array {
		return [
				'class' => ActionColumn::class,
				'template' => '{provision} {problem-status} {view} {update} {delete}',
				'controller' => '/settlement/calculation',
				'buttons' => [
					'problem-status' => static function (string $url, IssuePayCalculation $model): string {
						if ($model->isPayed()) {
							return '';
						}
						return Html::a(Html::icon('warning-sign'),
							['/settlement/calculation-problem/set', 'id' => $model->id],
							[
								'title' => Yii::t('backend', 'Set problem status'),
								'aria-label' => Yii::t('backend', 'Set problem status'),
							]);
					},
					'provision' => static function (string $url, IssuePayCalculation $model) {
						return Yii::$app->user->can(User::PERMISSION_PROVISION)
							? Html::a(Html::icon('usd'),
								['/provision/settlement/set', 'id' => $model->id],
								[
									'title' => Yii::t('backend', 'Set provisions'),
									'aria-label' => Yii::t('backend', 'Set provisions'),
								])
							: '';
					},
				],

				'contentOptions' => [
					'class' => 'd-inline-flex width-100 justify-center',
				],
			];
	}
}
