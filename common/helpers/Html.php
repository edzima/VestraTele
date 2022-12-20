<?php

namespace common\helpers;

use common\models\issue\IssueInterface;
use common\models\issue\IssueType;
use common\models\settlement\PayedInterface;
use Yii;
use yii\bootstrap\BaseHtml;

class Html extends BaseHtml {

	protected const URL_HELPER = Url::class;

	public static function telLink($text, $email = null, $options = []) {
		$options['href'] = 'tel:' . ($email === null ? $text : $email);
		return static::tag('a', $text, $options);
	}

	public static function issueLink(IssueInterface $issue, array $options = []): string {
		/** @var $url Url */
		$url = static::URL_HELPER;
		return static::a($issue->getIssueName(), $url::issueView($issue->getIssueId()), $options);
	}

	public static function payStatusRowOptions(PayedInterface $pay): array {
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
			1 => Yii::t('common', 'Yes'),
			0 => Yii::t('common', 'No'),
		];
	}

	public static function addNoPrintClass(array &$options): void {
		static::addCssClass($options, 'no-print');
	}

	public static function issueParentTypeItems(): array {
		/** @var $url Url */
		$url = static::URL_HELPER;
		$param = $url::PARAM_ISSUE_PARENT_TYPE;
		$items = [];
		$models = IssueType::getParents();

		foreach ($models as $model) {
			$items[] = [
				'url' => [$url::ROUTE_ISSUE_INDEX, $param => $model->id],
				'label' => $model->name,
			];
		}
		if (!empty($items)) {
			$items[] = [
				'url' => [$url::ROUTE_ISSUE_INDEX],
				'label' => Yii::t('issue', 'All Issues'),
				'active' => !isset(Yii::$app->request->getQueryParams()[$param]),
			];
		}

		return $items;
	}

}
