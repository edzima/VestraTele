<?php

namespace common\modules\lead\widgets;

use common\helpers\Html;
use common\modules\lead\models\LeadInterface;
use Yii;
use yii\data\ArrayDataProvider;
use yii\widgets\ListView;

class SameContactsListWidget extends ListView {

	public LeadInterface $model;
	public bool $withType = false;

	public $itemView = '@common/modules/lead/widgets/views/_same-contact';
	public $emptyText = false;
	public $summary = false;

	public $layout = "{header}\n{items}\n{pager}";

	public string $headerTag = 'h3';
	public array $headerOptions = [];

	public function init() {
		if ($this->dataProvider === null) {
			$this->dataProvider = new ArrayDataProvider([
				'models' => $this->getModels(),
			]);
		}
		parent::init();
	}

	public function renderSection($name) {
		if ($name === '{header}') {
			return $this->renderHeader();
		}
		return parent::renderSection($name);
	}

	public function renderHeader(): string {
		if ($this->dataProvider->getCount() > 0) {
			return Html::tag($this->headerTag, Yii::t('lead', 'Same Contacts Leads'), $this->headerOptions);
		}
		return '';
	}

	private function getModels(): array {
		return $this->model->getSameContacts($this->withType);
	}

}
