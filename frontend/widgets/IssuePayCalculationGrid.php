<?php

namespace frontend\widgets;

use common\models\issue\IssuePayCalculation;
use common\models\user\User;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\IssuePayCalculationGrid as BaseIssuePayCalculationGrid;
use common\widgets\grid\IssueTypeColumn;
use frontend\helpers\Html;
use frontend\helpers\Url;
use frontend\models\search\IssuePayCalculationSearch;
use Yii;

class IssuePayCalculationGrid extends BaseIssuePayCalculationGrid {

	public string $issueColumn = IssueColumn::class;
	public bool $withOwner = false;
	public bool $withStageOnCreate = false;
	public string $valueTypeIssueType = IssueTypeColumn::VALUE_NAME_WITH_SHORT;

	protected function actionColumn(): array {
		return [
			'class' => ActionColumn::class,
			'template' => '{view} {note}',
			'visibleButtons' => [
				'note' => Yii::$app->user->can(User::PERMISSION_NOTE),
			],
			'buttons' => [
				'note' => function ($url, IssuePayCalculation $model): string {
					return Html::a(
						'<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>',
						Url::toRoute([$this->noteRoute, 'id' => $model->id]),
						[
							'title' => Yii::t('common', 'Create note'),
							'aria-label' => Yii::t('common', 'Create note'),
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
