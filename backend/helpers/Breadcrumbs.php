<?php

namespace backend\helpers;

use common\models\issue\IssueInterface;
use common\models\issue\IssuePayCalculation;
use common\models\user\User;
use Yii;

class Breadcrumbs {

	public static function issue(IssueInterface $model, bool $withCustomer = true): array {
		$breadcrumbs = [];
		if ($withCustomer && $model->getIssueModel()->customer) {
			$breadcrumbs = static::customer($model->getIssueModel()->customer);
		}
		$breadcrumbs[] = ['label' => Yii::t('backend', 'Issues'), 'url' => ['/issue/issue/index']];
		$breadcrumbs[] = ['label' => $model->getIssueName(), 'url' => Url::issueView($model->getIssueId())];
		return $breadcrumbs;
	}

	public static function settlement(IssuePayCalculation $model, bool $withView = true): array {
		$breadcrumbs = [];
		if (Yii::$app->user->can(User::ROLE_BOOKKEEPER)) {
			$breadcrumbs[] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['index']];
			$breadcrumbs[] = ['label' => $model->getIssueName(), 'url' => ['/settlement/calculation/issue', 'id' => $model->getIssueId()]];
		}
		if ($withView) {
			$breadcrumbs[] = ['label' => $model->getTypeName(), 'url' => ['/settlement/calculation/view', 'id' => $model->id]];
		}
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
}
