<?php

namespace common\helpers;

use common\models\issue\IssueInterface;
use yii\bootstrap\BaseHtml;

class Html extends BaseHtml {

	protected const URL_HELPER = Url::class;

	public static function issueLink(IssueInterface $issue, array $options = []): string {
		/** @var $url Url */
		$url = static::URL_HELPER;
		return static::a($issue->getIssueName(), $url::issueView($issue->getIssueId()), $options);
	}

}
