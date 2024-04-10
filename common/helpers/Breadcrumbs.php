<?php

namespace common\helpers;

use common\models\issue\IssueInterface;
use common\models\user\User;
use Yii;

class Breadcrumbs {

	public static function issue(IssueInterface $model, bool $withCustomer = true): array {
		$breadcrumbs = [];
		if (!YII_IS_FRONTEND && $withCustomer && $model->getIssueModel()->customer) {
			$breadcrumbs = static::customer($model->getIssueModel()->customer);
		}
		$breadcrumbs[] = static::issues();
		if ($model->getIssueType()->getParentName()) {
			$breadcrumbs[] = ['label' => $model->getIssueType()->getParentName(), 'url' => [Url::issuesIndexRoute(), Url::PARAM_ISSUE_PARENT_TYPE => $model->getIssueType()->parent_id]];
		}
		$breadcrumbs[] = ['label' => $model->getIssueName(), 'url' => [Url::issueViewRoute(), 'id' => $model->getIssueId()]];
		return $breadcrumbs;
	}

	public static function customer(User $model): array {
		return [
			['label' => Yii::t('backend', 'Customers'), 'url' => ['/user/customer/index']],
			[
				'label' => $model->getFullName(), 'url' => ['/user/customer/view', 'id' => $model->id],
			],
		];
	}

	public static function issues(): array {
		return ['label' => Yii::t('issue', 'Issues'), 'url' => [Url::issuesIndexRoute()]];
	}

}
