<?php

namespace frontend\helpers;

use common\models\issue\IssueInterface;
use Yii;

class Breadcrumbs {

	public static function issue(IssueInterface $model, bool $withCustomer = true): array {
		$breadcrumbs = [];
		if ($model->getIssueModel()->customer) {
			$breadcrumbs[] = $model->getIssueModel()->customer->getFullName();
		}
		$breadcrumbs[] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/index']];
		$breadcrumbs[] = ['label' => $model->getIssueName(), 'url' => Url::issueView($model->getIssueId())];
		return $breadcrumbs;
	}

}
