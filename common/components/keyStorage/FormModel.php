<?php

namespace common\components\keyStorage;

use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Json;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 * @author Lukasz Wojda <lukasz.wojda@protonmail.com>
 * @var array $keys Array of the keyStorage keys to be handled by this model ($key => $config)
 * Example:
 * [
 *   'keys' => [
 *       'frontend.maintenance' => [
 *           'label' => 'Maintenance mode',
 *           'type' => FormModel::TYPE_CHECKBOX,
 *           'rules' => [ ... validation rules ...]
 *           // 'items' => ['a' => 'b']  - For lists like TYPE_DROPBOX,
 *           // 'options' => [ ... ... ] - Options that will be passed to ActiveInput or widget
 *           // 'widget' => 'yii\jui\Datepicker' - Widget class name if TYPE_WIDGET
 *       ]
 *    ]
 * ]
 */
class FormModel extends Model {

	public const TYPE_DROPDOWN = 'dropdownList';
	public const TYPE_TEXTINPUT = 'textInput';
	public const TYPE_TEXTAREA = 'textarea';
	public const TYPE_CHECKBOX = 'checkbox';
	public const TYPE_RADIOLIST = 'radioList';
	public const TYPE_CHECKBOXLIST = 'checkboxList';
	public const TYPE_WIDGET = 'widget';

	/**
	 * @var array
	 */
	protected array $keys = [];
	/**
	 * @var array
	 */
	protected array $map = [];

	protected array $json = [];

	/**
	 * @var string|array|KeyStorage
	 */
	public $keyStorage = 'keyStorage';

	/**
	 * @var array
	 */
	protected $attributes;

	public function init(): void {
		$this->keyStorage = Instance::ensure($this->keyStorage, KeyStorage::class);
		parent::init();
	}

	/**
	 * @param $keys
	 */
	public function setKeys(array $keys): void {
		$variablized = $values = [];
		foreach ($keys as $key => $data) {
			$variablizedKey = static::attributeName($key);
			$this->map[$variablizedKey] = $key;
			$value = $this->getKeyStorage()->get($key, null, false);
			$json = ArrayHelper::getValue($data, 'json', false);
			if ($json) {
				$this->json[$variablizedKey] = $value;
				$value = Json::decode($value);
			}
			$values[$variablizedKey] = $value;
			$variablized[$variablizedKey] = $data;
		}
		$this->keys = $variablized;
		foreach ($values as $k => $v) {
			$this->setAttribute($k, $v);
		}
		parent::init();
	}

	protected function getKeyStorage(): KeyStorage {
		$this->keyStorage = Instance::ensure($this->keyStorage, KeyStorage::class);
		return $this->keyStorage;
	}

	/**
	 * @return array
	 */
	public function getKeys(): array {
		return $this->keys;
	}

	/**
	 * Returns the list of attribute names.
	 * By default, this method returns all public non-static properties of the class.
	 * You may override this method to change the default behavior.
	 *
	 * @return array list of attribute names.
	 */
	public function attributes(): array {
		$names = [];
		foreach ($this->keys as $attribute => $values) {
			$names[] = $attribute;
		}

		return $names;
	}

	public function rules(): array {
		$rules = [];
		foreach ($this->keys as $attribute => $data) {
			$attributeRules = ArrayHelper::getValue($data, 'rules', []);
			if (!empty($attributeRules)) {
				foreach ($attributeRules as $rule) {
					array_unshift($rule, $attribute);
					$rules[] = $rule;
				}
			} else {
				$rules[] = [$attribute, 'safe'];
			}
		}

		return $rules;
	}

	public function attributeLabels(): array {
		$labels = [];
		foreach ($this->keys as $attribute => $data) {
			$label = is_array($data) ? ArrayHelper::getValue($data, 'label') : $data;
			$labels[$attribute] = $label;
		}

		return $labels;
	}

	/**
	 * @param bool $validate
	 * @return bool
	 * @throws Exception
	 */
	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		foreach ($this->attributes as $variablizedKey => $value) {

			$originalKey = ArrayHelper::getValue($this->map, $variablizedKey);

			if (!$originalKey) {
				throw new Exception();
			}

			if (array_key_exists($variablizedKey, $this->json)) {
				$value = Json::encode($value);
			}

			$this->getKeyStorage()->set($originalKey, $value);
		}

		return true;
	}

	/**
	 * PHP getter magic method.
	 * This method is overridden so that attributes and related objects can be accessed like properties.
	 *
	 * @param string $name property name
	 * @return mixed property value
	 * {@inheritDoc}
	 */
	public function __get($name) {
		if ($this->hasAttribute($name)) {
			return $this->attributes[$name];
		}

		return parent::__get($name);
	}

	/**
	 * PHP setter magic method.
	 * This method is overridden so that AR attributes can be accessed like properties.
	 *
	 * @param string $name property name
	 * @param mixed $value property value
	 */
	public function __set($name, $value) {
		if ($this->hasAttribute($name)) {
			$this->attributes[$name] = $value;
		} else {
			parent::__set($name, $value);
		}
	}

	/**
	 * Checks if a property value is null.
	 * This method overrides the parent implementation by checking if the named attribute is null or not.
	 *
	 * @param string $name the property name or the event name
	 * @return bool whether the property value is null
	 */
	public function __isset($name) {
		try {
			return $this->__get($name) !== null;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Sets a component property to be null.
	 * This method overrides the parent implementation by clearing
	 * the specified attribute value.
	 *
	 * @param string $name the property name or the event name
	 */
	public function __unset($name) {
		if ($this->hasAttribute($name)) {
			unset($this->attributes[$name]);
		}
	}

	/**
	 * Returns a value indicating whether the model has an attribute with the specified name.
	 *
	 * @param string $name the name of the attribute
	 * @return bool whether the model has an attribute with the specified name.
	 */
	public function hasAttribute(string $name): bool {
		return isset($this->attributes[$name]) || in_array($name, $this->attributes(), false);
	}

	/**
	 * Sets the named attribute value.
	 *
	 * @param string $name the attribute name
	 * @param mixed $value the attribute value.
	 * @throws InvalidArgumentException if the named attribute does not exist.
	 * @see hasAttribute()
	 */
	public function setAttribute(string $name, $value): void {
		if (!$this->hasAttribute($name)) {
			throw new InvalidArgumentException(static::class . ' has no attribute named "' . $name . '".');
		}
		$this->attributes[$name] = $value;
	}

	public static function attributeName(string $name): string {
		return Inflector::variablize($name);
	}
}
