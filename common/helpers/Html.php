<?php

namespace common\helpers;

use common\models\issue\IssueInterface;
use common\models\settlement\PayInterface;
use Yii;
use yii\bootstrap\BaseHtml;

class Html extends BaseHtml {

	protected const URL_HELPER = Url::class;

	public static function issueLink(IssueInterface $issue, array $options = []): string {
		/** @var $url Url */
		$url = static::URL_HELPER;
		return static::a($issue->getIssueName(), $url::issueView($issue->getIssueId()), $options);
	}

	public static function payStatusRowOptions(PayInterface $pay): array {
		$options = [];
		if ($pay->isPayed()) {
			static::addCssClass($options, 'payed-row success');
		} elseif ($pay->isDelayed()) {
			static::addCssClass($options, 'delayed-row warning');
		}
		return $options;
	}

	public static function booleanDropdownList(): array {
		return [
			0 => Yii::t('common', 'Yes'),
			1 => Yii::t('common', 'No'),
		];
	}

}
