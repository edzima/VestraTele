<?php

namespace common\models\relation;

interface RelationModel {

	public function getFromId(): int;

	public function getToId(): int;

	public function getType(): string;

	public static function fromTargetClass(): string;

	public static function toTargetClass(): string;

	public static function typeAttribute(): string;

	public static function fromAttribute(): string;

	public static function toAttribute(): string;

}
