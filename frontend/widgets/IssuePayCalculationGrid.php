<?php

namespace frontend\widgets;

use common\components\rbac\SettlementTypeAccessManager;
use common\models\issue\IssuePayCalculation;
use common\models\user\User;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\IssuePayCalculationGrid as BaseIssuePayCalculationGrid;
use frontend\helpers\Html;
use frontend\helpers\Url;
use frontend\models\search\IssuePayCalculationSearch;
use Yii;

class IssuePayCalculationGrid extends BaseIssuePayCalculationGrid {

	public string $issueColumn = IssueColumn::class;
	public bool $withOwner = false;
	public bool $withStageOnCreate = false;

	protected function actionColumn(): array {
		return [
			'class' => ActionColumn::class,
			'template' => '{note} {view}',
			'visibleButtons' => [
				'note' => Yii::$app->user->can(User::PERMISSION_NOTE),
				'view' => function (IssuePayCalculation $model): bool {
					return $model->type->hasAccess($this->userId, SettlementTypeAccessManager::ACTION_VIEW);
				},
			],
			'buttons' => [
				'note' => function ($url, IssuePayCalculation $model): string {
					return Html::a(
						'<i class="fa fa-comments" aria-hidden="true"></i>',
						Url::toRoute([$this->noteRoute, 'id' => $model->id]),
						[
							'title' => Yii::t('issue', 'Create Note'),
							'aria-label' => Yii::t('issue', 'Create Note'),
						]);
				},
			],
			'controller' => 'settlement',
		];
	}

	protected function problemStatusFilter(): array {
		return IssuePayCalculationSearch::getProblemStatusesNames();
	}
}
