<?php

namespace common\modules\calendar\models;

use common\helpers\Html;
use common\models\issue\Summon;

class LawsuitSummonCalendarEvent extends SummonCalendarEvent {

	public string $is = self::IS_DEADLINE;

	public function setModel(Summon $model): void {
		parent::setModel($model);
		if ($model->isRealized() || $model->isUnrealized()) {
			$this->classNames[] = static::CLASS_TRANSPARENT;
		}
	}

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
		return $this->getModel()->type->getOptions()->lawsuitCalendarBackground;
	}

}
