<?php

namespace common\modules\reminder\models;

interface ReminderInterface {

	public function getUserId(): ?int;

	public function isDone(): bool;

	public function isDelayed(): bool;

	public function getDateAt(): string;

	public function getDoneAt(): ?string;

	public function getPriority(): int;
}
