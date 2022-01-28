<?php

namespace common\assets;

use yii\helpers\Json;
use yii\web\AssetBundle;

class TooltipAsset extends AssetBundle {

	public const DEFAULT_ATTRIBUTE_NAME = 'data-tippy-content';

	public $baseUrl = 'https://unpkg.com/';

	public $css = ['tippy.js@5/dist/tippy.css'];
	public $js = [
		'popper.js@1',
		YII_ENV_DEV
			? 'tippy.js@5/dist/tippy-bundle.iife.js'
			: 'tippy.js@5',
	];

	public static function initScript(?string $selector = null, array $options = []): string {
		if ($selector === null) {
			$selector = static::defaultSelector();
		}
		if (!empty($options)) {
			$json = Json::encode($options);
			return "tippy('$selector', $json);";
		}
		return "tippy('$selector');";
	}

	public static function defaultSelector(string $parent = ''): string {
		$selector = '[' . static::DEFAULT_ATTRIBUTE_NAME . ']';
		if ($parent) {
			$selector = $parent . ' ' . $selector;
		}
		return $selector;
	}

}
