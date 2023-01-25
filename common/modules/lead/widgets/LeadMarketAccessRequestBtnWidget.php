<?php

namespace common\modules\lead\widgets;

use common\helpers\Html;
use common\helpers\Url;
use common\modules\lead\models\forms\LeadMarketAccessRequest;
use common\widgets\ButtonDropdown;
use Yii;

class LeadMarketAccessRequestBtnWidget extends ButtonDropdown {

	public int $marketId;

	public bool $inGrid = true;

	public $tagName = 'a';

	public string $route = '/lead/market-user/access-request';

	public int $defaultDay = LeadMarketAccessRequest::DEFAULT_DAYS;
	public $split = true;

	public array $itemOptions = [];

	public array $days = [
		2,
		4,
		7,
		14,
	];

	public function init(): void {
		parent::init();
		if ($this->label === 'Button') {
			$this->label = '<i class="fa fa-unlock" aria-hidden="true"></i>';
			$this->encodeLabel = false;
			if (!$this->inGrid) {
				$this->label .= ' ' . $this->itemTitle($this->defaultDay);
			}
		}

		if (!isset($this->dropdown['items'])) {
			$this->dropdown['items'] = $this->defaultItems();
		}
		if (!isset($this->options['href'])) {
			$this->options['href'] = $this->getItemUrl($this->defaultDay);
		}
		if (!isset($this->options['title'])) {
			$this->options['title'] = $this->itemTitle($this->defaultDay);
		}
		if (!isset($this->options['aria-label'])) {
			$this->options['aria-label'] = $this->itemTitle($this->defaultDay);
		}

		if ($this->inGrid) {
			//@todo remove this after migrate BS4 (add data-boundary="viewport")
			//@see https://stackoverflow.com/questions/26018756/bootstrap-button-drop-down-inside-responsive-table-not-visible-because-of-scroll#answer-51992907
			$this->view->registerJs("$('.table-responsive').on('show.bs.dropdown', function () {
	     	$('.table-responsive').css('overflow', 'inherit' );
			});
	
			$('.table-responsive').on('hide.bs.dropdown', function () {
				$('.table-responsive').css( 'overflow', 'auto' );
			})"
			);
		}
	}

	public function itemTitle(int $days): string {
		return Yii::t('lead', 'Request Access ({days} days)', [
			'days' => $this->defaultDay,
		]);
	}

	protected function defaultItems(): array {
		$items = [];
		foreach ($this->days as $day) {
			$options = $this->itemOptions;
			if ($day === $this->defaultDay) {
				Html::addCssClass($options, 'active');
			}
			$items[] = [
				'label' => $day,
				'url' => $this->getItemUrl($day),
				'options' => $options,
			];
		}
		return $items;
	}

	public function getItemUrl(int $day): string {
		return Url::to([$this->route, 'days' => $day, 'market_id' => $this->marketId]);
	}
}
