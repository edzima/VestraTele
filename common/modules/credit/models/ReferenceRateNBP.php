<?php

namespace common\modules\credit\models;

use SimpleXMLElement;

class ReferenceRateNBP {

	private const XML_ATTRIBUTE_FROM_AT = 'obowiazuje_od';
	private const XML_ATTRIBUTE_POSITION_ID = 'id';
	private const XML_ATTRIBUTE_POSITION_VALUE = 'oprocentowanie';

	public string $fromAt;
	public ?string $toAt = null;

	public float $ref;
	public float $lom;
	public float $dep;
	public float $red;
	public float $dys;

	public function isForDate(string $date): bool {
		if ($this->fromAt <= $date) {
			if ($this->toAt === null || $this->toAt > $date) {
				return true;
			}
		}
		return false;
	}

	public static function createModel(SimpleXMLElement $element): self {
		$model = new static();
		$model->fromAt = (string) $element->attributes()[static::XML_ATTRIBUTE_FROM_AT];
		foreach ($element->pozycja as $poz) {
			$attr = $poz->attributes();
			$id = (string) $attr[static::XML_ATTRIBUTE_POSITION_ID];
			$value = static::getValue($attr);
			switch ($id) {
				case 'ref':
					$model->ref = $value;
					break;
				case 'lom':
					$model->lom = $value;
					break;
				case 'dep':
					$model->dep = $value;
					break;
				case 'red':
					$model->red = $value;
					break;
				case 'dys':
					$model->dys = $value;
					break;
			}
		}
		return $model;
	}

	private static function getValue(SimpleXMLElement $element): float {
		$value = (string) $element[static::XML_ATTRIBUTE_POSITION_VALUE];
		return (float) strtr($value, ',', '.');
	}

	/**
	 * @param SimpleXMLElement $element
	 * @return static[]
	 */
	public static function createModels(SimpleXMLElement $element): array {
		$models = [];
		foreach ($element->pozycje as $node) {
			$model = static::createModel($node);
			$models[$model->fromAt] = $model;
		}
		$models = array_reverse($models);
		foreach ($models as &$model) {
			/**
			 * @var static|null $next
			 */
			$next = next($models);
			if ($next) {
				$next->toAt = $model->fromAt;
			}
		}
		return $models;
	}
}
