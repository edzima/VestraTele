<?php

namespace common\modules\calendar\models;

use yii\base\Model;

class FullCalendarEvent extends Model {

	public string $id;
	public string $groupId;
	public bool $allDay;
	public string $start;
	public ?string $end = null;

	public ?string $startTime = null;
	public ?string $startRecur = null;
	public ?string $endRecur = null;
	public string $title;
	public ?string $url = null;
	public ?array $classNames = null;
	public ?bool $editable = null;
	public ?bool $startEditable = null;
	public ?bool $durationEditable = null;
	public ?bool $resourceEditable = null;
	public ?string $backgroundColor = null;
	public ?string $borderColor = null;
	public ?string $textColor = null;
	public ?array $source = null;
	public ?array $extendedProps = null;

	//extra field, not from FullCalendar
	public ?string $tooltipContent = null;

	public function toArray(array $fields = [], array $expand = [], $recursive = true) {
		$array = parent::toArray($fields, $expand, $recursive);
		return array_filter($array, static function ($value): bool {
			return $value !== null;
		});
	}
}
