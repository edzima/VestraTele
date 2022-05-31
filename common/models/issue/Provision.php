<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-03-02
 * Time: 10:59
 */

namespace common\models\issue;

use yii\base\BaseObject;
use yii\base\InvalidConfigException;

/**
 * @deprecated
 */
class Provision extends BaseObject {

	public const TYPE_PERCENTAGE = 1;
	public const TYPE_MULTIPLICITY = 2;

	private $type;
	private $base;
	private $value;

	public function __construct(int $type, array $config = []) {
		if (!isset(static::getTypesNames()[$type])) {
			throw new InvalidConfigException('Invalid provision type.');
		}
		$this->type = $type;
		parent::__construct($config);
	}

	public function __toString(): string {
		return "{$this->getValue()} {$this->getTypeName()}";
	}

	public function getSum(): string {
		if ($this->type === static::TYPE_PERCENTAGE) {
			return $this->getBase() * $this->getValue() / 100;
		}
		return $this->getBase() * $this->getValue();
	}

	public function getTypeName(): string {
		return static::getTypesNames()[$this->type];
	}

	public function getBase(): ?float {
		return $this->base;
	}

	public function setBase(float $base): void {
		$this->base = $base;
	}

	public function getValue(): ?float {
		return $this->value;
	}

	public function setValue(float $value): void {
		$this->value = $value;
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_PERCENTAGE => '%',
			static::TYPE_MULTIPLICITY => 'X',
		];
	}

}
