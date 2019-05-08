<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-03-24
 * Time: 18:45
 */

namespace common\behaviors;

use yii\base\Behavior;
use yii\base\InvalidConfigException;

class DateIDBehavior extends Behavior {

	public $idField = 'id';
	public $dateField = 'created_at';
	public $timestamp = false;
	public $template = '{id}/{m}/{Y}';

	public function getLongId(): string {
		if ($this->template instanceof \Closure) {
			$template = $this->template;
			return $template($this->owner);
		}
		return $this->generateLongId();
	}

	private function generateLongId(): string {
		$timestamp = $this->timestamp ? $this->owner->{$this->dateField} : strtotime($this->owner->{$this->dateField});
		preg_match_all('/{(.*?)}/', $this->template, $match);
		$parts = [];
		foreach ($match[1] as $key => $value) {
			if ($value === 'id') {
				$value = $this->owner->{$this->idField};
			} else {
				$value = date($value, $timestamp);
			}
			if ($value !== false) {
				$parts[$match[0][$key]] = $value;
			}
		}
		if (empty($parts)) {
			throw new InvalidConfigException('Template values must has {id} part and other date() format parts. ');
		}
		return strtr($this->template, $parts);
	}

}