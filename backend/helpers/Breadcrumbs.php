<?php

namespace backend\helpers;

use common\models\issue\Issue;
use common\models\user\User;
use Yii;

class Breadcrumbs {

	public static function issue(Issue $model, bool $withCustomer = true): array {
		$breadcrumbs = [];
		if ($withCustomer && $model->customer) {
			$breadcrumbs = static::customer($model->customer);
		}
		$breadcrumbs[] = ['label' => Yii::t('backend', 'Issues'), 'url' => ['/issue/issue/index']];
		$breadcrumbs[] = ['label' => $model->longId, 'url' => Url::issueView($model->id)];
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
