<?php

namespace backend\modules\settlement\widgets;

use backend\widgets\IssueColumn;
use common\models\issue\IssuePayCalculation;
use common\models\user\User;
use common\models\user\Worker;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\IssuePayCalculationGrid as BaseIssuePayCalculationGrid;
use frontend\helpers\Url;
use Yii;
use yii\bootstrap\Html;

class IssuePayCalculationGrid extends BaseIssuePayCalculationGrid {

	public string $issueColumn = IssueColumn::class;

	public ?string $noteRoute = '/issue/note/create-settlement';

	public function init(): void {
		if (Yii::$app->user->can(User::ROLE_BOOKKEEPER)) {
			$this->withValueSummary = true;
		}
		parent::init();
	}

	protected function actionColumn(): array {
		return [
			'class' => ActionColumn::class,
			'template' => '{note} {provision} {problem-status} {view} {update} {delete}',
			'controller' => '/settlement/calculation',
			'visible' => [
				'delete' => function (IssuePayCalculation $model): bool {
					return $model->owner_id === Yii::$app->user->getId() || Yii::$app->user->can(Worker::ROLE_BOOKKEEPER);
				},
			],
			'buttons' => [
				'note' => function ($url, IssuePayCalculation $model): string {
					return \frontend\helpers\Html::a(
						'<i class="fa fa-comments" aria-hidden="true"></i>',
						Url::toRoute([$this->noteRoute, 'id' => $model->id]),
						[
							'title' => Yii::t('issue', 'Create Note'),
							'aria-label' => Yii::t('issue', 'Create Note'),
						]);
				},
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
							['/provision/settlement/view', 'id' => $model->id],
							[
								'title' => Yii::t('provision', 'Provisions'),
								'aria-label' => Yii::t('provision', 'Provisions'),
							])
						: '';
				},
			],
		];
	}
}
