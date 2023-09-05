<?php

namespace frontend\helpers;

use common\models\issue\IssueInterface;
use Yii;

class Breadcrumbs {

	public static function issue(IssueInterface $model, bool $withIssueLink = true): array {
		$breadcrumbs = [];
		if ($model->getIssueModel()->customer) {
			$breadcrumbs[] = $model->getIssueModel()->customer->getFullName();
		}
		$breadcrumbs[] = ['label' => Yii::t('issue', 'Issues'), 'url' => [Url::ROUTE_ISSUE_INDEX]];
		if ($model->getIssueType()->parent) {
			$breadcrumbs[] = [
				'label' => $model->getIssueType()->parent->name,
				'url' => Url::issuesParentType($model->getIssueType()->parent_id),
			];
		}
		$breadcrumbs[] = $withIssueLink
			? ['label' => $model->getIssueName(), 'url' => Url::issueView($model->getIssueId())]
			: $model->getIssueName();
		return $breadcrumbs;
	}

}
