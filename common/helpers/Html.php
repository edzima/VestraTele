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

	public static function issueParentTypeItems(array $config = []): array {
		/** @var $url Url */
		$url = static::URL_HELPER;
		$param = $url::PARAM_ISSUE_PARENT_TYPE;
		$items = [];
		$models = IssueType::getParents();
		$route = ArrayHelper::getValue($config, 'route', [$url::ROUTE_ISSUE_INDEX]);
		$queryParam = Yii::$app->request->getQueryParams()[$param] ?? null;

		foreach ($models as $model) {
			$typeRoute = $route;
			$typeRoute[$param] = $model->id;
			$items[] = [
				'url' => $typeRoute,
				'label' => $model->name,
				'active' => (int) $queryParam === $model->id,
			];
		}
		if (!empty($items)) {
			$items[] = [
				'url' => $route,
				'label' => Yii::t('issue', 'All Issues'),
				'active' => empty($queryParam),
			];
		}

		return $items;
	}

	public static function hexToRgb(string $hex, bool $alpha = false): array {
		$hex = str_replace('#', '', $hex);
		$length = strlen($hex);
		$rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
		$rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
		$rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
		if ($alpha) {
			$rgb['a'] = $alpha;
		}
		return $rgb;
	}

	public static function cssRgbValueFromHex(string $hex, bool $alpha = false): string {
		$rgb = static::hexToRgb($hex, $alpha);
		return implode(array_keys($rgb)) . '(' . implode(',', $rgb) . ')';
	}

	public static function luminanceDark(string $hexcolor, float $percent) {
		if (strlen($hexcolor) < 6) {
			$hexcolor = $hexcolor[0] . $hexcolor[0] . $hexcolor[1] . $hexcolor[1] . $hexcolor[2] . $hexcolor[2];
		}
		$hexcolor = array_map('hexdec', str_split(str_pad(str_replace('#', '', $hexcolor), 6, '0'), 2));

		foreach ($hexcolor as $i => $color) {
			$from = $percent < 0 ? 0 : $color;
			$to = $percent < 0 ? $color : 0;
			$pvalue = ceil(($to - $from) * $percent);
			$hexcolor[$i] = str_pad(dechex($color + $pvalue), 2, '0', STR_PAD_LEFT);
		}

		return '#' . implode($hexcolor);
	}

}
