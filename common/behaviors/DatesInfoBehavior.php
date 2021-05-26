<?php

namespace common\behaviors;

use Closure;
use Yii;
use yii\base\Behavior;

class DatesInfoBehavior extends Behavior {

	public string $createdAtAttribute = 'created_at';
	public string $updatedAtAttribute = 'updated_at';

	public ?Closure $formatter = null;

	public function init(): void {
		if ($this->formatter === null) {
			$this->formatter = static function ($value): string {
				return Yii::$app->formatter->asDatetime($value);
			};
		}
		parent::init();
	}

	public function getFormattedDates(): string {
		$createdAt = $this->owner->{$this->createdAtAttribute};
		$updatedAt = $this->owner->{$this->updatedAtAttribute};
		if ($createdAt === $updatedAt) {
			return $this->format($createdAt);
		}
		return Yii::t('common', '{createdAt} - (updated: {updatedAt})', [
			'createdAt' => $this->format($createdAt),
			'updatedAt' => $this->format($updatedAt),
		]);
	}

	private function format($value): string {
		return call_user_func($this->formatter, $value);
	}
}
