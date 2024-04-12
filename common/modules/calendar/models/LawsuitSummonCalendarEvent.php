<?php

namespace common\modules\calendar\models;

use common\helpers\Html;

class LawsuitSummonCalendarEvent extends SummonCalendarEvent {

	public string $is = self::IS_DEADLINE;

	protected function getTitle(): string {
		$customer = $this->getModel()->issue->customer;
		$title = Html::encode($this->getModel()->title . ': ' . $customer->profile->lastname);

		$title .= "\n" . Html::encode($this->getModel()->issue->entityResponsible->name);
		return $title;
	}

	protected function getTooltipContent(): string {
		return $this->getModel()->getStatusName();
	}

	protected function getPhone(): ?string {
		return null;
	}

	protected function getBackgroundColor(): string {
		return 'green';
	}
}
