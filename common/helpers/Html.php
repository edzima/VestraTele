<?php

namespace common\helpers;

use common\behaviors\IssueTypeParentIdAction;
use common\models\issue\IssueInterface;
use common\models\issue\IssueType;
use common\models\settlement\PayedInterface;
use common\modules\file\models\File;
use Yii;
use yii\bootstrap\BaseHtml;
use yii\helpers\VarDumper;

class Html extends BaseHtml {

	protected const URL_HELPER = Url::class;

	public static function faicon(string $name, array $options = []): string {
		$options['aria-hidden'] = true;
		static::addCssClass($options, 'fa fa-' . $name);
		return static::tag('i', '', $options);
	}

	public static function telLink($text, $email = null, $options = []) {
		$options['href'] = 'tel:' . ($email === null ? $text : $email);
		return static::tag('a', $text, $options);
	}

	public static function issueLink(IssueInterface $issue, array $options = []): string {
		/** @var $url Url */
		$url = static::URL_HELPER;
		$issueUrl = $url::issueView($issue->getIssueId());
		return static::a($issue->getIssueName(), $issueUrl, $options);
	}

	public static function issueFileLink(File $file, int $issue_id, $schema = false): string {
		$name = $file->getShortName() . '.' . $file->type;
		$name = Html::encode($name);
		$url = static::URL_HELPER;
		if ($file->isForUser(Yii::$app->user->getId())) {
			$url = $url::issueFileDownload($issue_id, $file->id, $schema);
			return Html::a($name, $url);
		}
		return $name;
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

	public static function issueMainTypesItems(array $config = []): array {
		/** @var $url Url */
		$url = static::URL_HELPER;
		$param = $url::PARAM_ISSUE_PARENT_TYPE;
		$items = [];
		$models = IssueType::getMainTypes();
		$route = ArrayHelper::getValue($config, 'route', [$url::ROUTE_ISSUE_INDEX]);
		$queryParam = Yii::$app->request->getQueryParams()[$param] ?? null;
		$withFavorite = ArrayHelper::getValue($config, 'withFavorite', false);
		$favoriteType = null;
		if ($withFavorite) {
			$favoriteType = Yii::$app->user->getFavoriteIssueType();
		}
		foreach ($models as $model) {

			$label = static::encode($model->name);
			if ($withFavorite) {
				$isFavorite = $model->id === $favoriteType;
				$favoriteConfig = ArrayHelper::getValue($config, 'favoriteConfig');
				$favoriteConfig['data-method'] = 'POST';

				static::addCssClass($favoriteConfig, 'favorite-link');
				if ($isFavorite) {
					static::addCssClass($favoriteConfig, 'active');
				}
				$favoriteLink = static::a(
					static::icon('star'),
					[
						'/user-settings/favorite-issue-type',
						'type_id' => !$isFavorite ? $model->id : null,
						'returnUrl' => Url::current(),
					],
					$favoriteConfig
				);
				$label .= $favoriteLink;
			}

			$typeRoute = $route;
			$typeRoute[$param] = $model->id;
			$item = ArrayHelper::getValue($config, 'itemOptions', []);
			$item = array_merge($item, [
				'url' => $typeRoute,
				'label' => $label,
				'encode' => false,
				'active' => (int) $queryParam === $model->id,
			]);
			$items[] = $item;
		}
		if (!empty($items)) {
			$typeRoute = $route;
			$allIssueParentType = IssueTypeParentIdAction::ISSUE_PARENT_TYPE_ALL;
			$typeRoute[$param] = $allIssueParentType;
			$items[] = [
				'url' => $typeRoute,
				'label' => Yii::t('issue', 'All Issues'),
				'active' => (int) $queryParam === $allIssueParentType || empty($queryParam),
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

	public static function dump(mixed $var, array $options = []): string {
		return static::tag('pre', VarDumper::dumpAsString($var), $options);
	}

}
