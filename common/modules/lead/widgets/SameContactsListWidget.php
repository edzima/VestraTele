<?php

namespace common\modules\lead\widgets;

use common\helpers\Html;
use common\modules\lead\models\ActiveLead;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;
use yii\widgets\ListView;

class SameContactsListWidget extends ListView {

	public ?ActiveLead $model = null;
	public bool $withType = false;
	public bool $withDialers = false;
	public bool $viewLink = false;
	public bool $visibleCustomerLink = false;
	public bool $updateLink = false;

	public $itemView = '@common/modules/lead/widgets/views/_same-contact';
	public $emptyText = false;
	public $summary = false;

	public $layout = "{header}\n{items}\n{pager}";

	public string $headerTag = 'h3';
	public array $headerOptions = [];
	public bool $archiveBtn = false;
	public bool $withArchiveBtn = true;
	public bool $withHeader = true;

	public function init() {
		if ($this->dataProvider === null) {
			if ($this->model === null) {
				throw new InvalidConfigException('$model must be set when dataProvider is not set.');
			}
			$this->dataProvider = new ArrayDataProvider([
				'models' => $this->getModels(),
			]);
		}
		$this->viewParams = [
			'viewLink' => $this->viewLink,
			'updateLink' => $this->updateLink,
			'withType' => $this->withType,
			'withDialers' => $this->withDialers,
			'visibleCustomerLink' => $this->visibleCustomerLink,
		];
		parent::init();
	}

	public function renderSection($name) {
		if ($name === '{header}') {
			return $this->renderHeader();
		}
		return parent::renderSection($name);
	}

	public function renderHeader(): string {
		if ($this->withHeader && $this->dataProvider->getCount() > 0) {
			return Html::tag($this->headerTag, $this->renderHeaderContent(), $this->headerOptions);
		}
		return '';
	}

	public function renderHeaderContent(): string {
		$content = Yii::t('lead', 'Same Contacts Leads');
		if (!$this->archiveBtn) {
			return $content;
		}
		return $content . $this->renderArchiveBtn();
	}

	private function getModels(): array {
		return $this->model->getSameContacts($this->withType);
	}

	private function renderArchiveBtn(): string {
		if (!$this->withArchiveBtn) {
			return '';
		}
		if ($this->model === null) {
			return '';
		}
		if (!$this->model->getSameContacts(true)) {
			return '';
		}
		$content = ArchiveSameContactButton::widget([
			'model' => $this->model,
		]);

		return Html::tag('span', $content, ['class' => 'btn-wrapper pull-right']);
	}

}
