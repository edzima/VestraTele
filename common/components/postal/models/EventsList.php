<?php

namespace common\components\postal\models;

class EventsList {

	/**
	 * @var Events[]|Events|null
	 */
	public $zdarzenie;

	/**
	 * @return Events[]
	 */
	public function getEvents(): array {
		if (is_array($this->zdarzenie)) {
			return $this->zdarzenie;
		}
		if (is_object($this->zdarzenie)) {
			return [
				$this->zdarzenie,
			];
		}
		return [];
	}
}
