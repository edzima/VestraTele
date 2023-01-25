<?php

namespace common\widgets;

use common\helpers\Url;
use Yii;
use yii\base\Model;
use yii\bootstrap\Nav;
use yii\helpers\Html;

class LastCurrentNextMonthNav extends Nav {

	public $route = ['index'];
	public array $extraParams = [];
	public string $linkDateFormat = 'Y-m';
	public string $modelDateFormat = 'Y-m-d';

	public $options = [
		'class' => 'nav-pills',
	];

	public Model $model;
	public string $dateFromAttribute = 'dateFrom';
	public string $dateToAttribute = 'dateTo';
	public ?string $dateFromParamName = null;
	public ?string $dateToParamName = null;

	public function init() {
		parent::init();
		if (empty($this->items)) {
			$this->items = $this->generateItems();
		}
	}

	protected function generateItems(): array {
		$items = [];
		$items[] = $this->getLastMonthItem();
		$items[] = $this->getCurrentMonthItem();
		$items[] = $this->getNextMonthItem();
		return $items;
	}

	private function getLastMonthItem(): array {
		$from = $this->getUrlDateString('first day of last month');
		$to = $this->getUrlDateString('last day of last month');
		return [
			'label' => Yii::t('common',
				'Last Month {month}', ['month' => $this->getLinkDateString('last month')]
			),
			'url' => $this->generateUrl(
				$from,
				$to
			),
			'active' => $this->isActive($from, $to),
		];
	}

	private function getCurrentMonthItem(): array {
		$from = $this->getUrlDateString('first day of this month');
		$to = $this->getUrlDateString('last day of this month');
		return [
			'label' => Yii::t('common',
				'Current Month {month}', ['month' => $this->getLinkDateString('this month')]
			),
			'url' => $this->generateUrl(
				$from,
				$to
			),
			'active' => $this->isActive($from, $to),
		];
	}

	private function getNextMonthItem(): array {
		$from = $this->getUrlDateString('first day of next month');
		$to = $this->getUrlDateString('last day of next month');
		return [
			'label' => Yii::t('common',
				'Next Month {month}', ['month' => $this->getLinkDateString('next month')]
			),
			'url' => $this->generateUrl(
				$from,
				$to
			),
			'active' => $this->isActive($from, $to),
		];
	}

	private function isActive(string $from, string $to): bool {
		return $this->model->{$this->dateFromAttribute} === $from
			&& $this->model->{$this->dateToAttribute} === $to;
	}

	private function getLinkDateString(string $datetime): string {
		return date($this->linkDateFormat, strtotime($datetime));
	}

	private function getUrlDateString(string $datetime): string {
		return date($this->modelDateFormat, strtotime($datetime));
	}

	protected function generateUrl(string $dateFromValue, string $dateToValue): string {
		$route = $this->route;
		$route[$this->getDateFromParamName()] = $dateFromValue;
		$route[$this->getDateToParamName()] = $dateToValue;
		return Url::to(array_merge($route, $this->extraParams));
	}

	protected function getDateFromParamName(): string {
		return $this->dateFromParamName ?: Html::getInputName($this->model, $this->dateFromAttribute);
	}

	protected function getDateToParamName(): string {
		return $this->dateToParamName ?: Html::getInputName($this->model, $this->dateToAttribute);
	}

}
