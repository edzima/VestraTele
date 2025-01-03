<?php

namespace common\modules\lead\widgets;

use common\helpers\Url;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\Module;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\DateTimeColumn;
use common\widgets\GridView;
use kartik\grid\ExpandRowColumn;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;

class SameContactsGridView extends GridView {

	public ?ActiveLead $model = null;
	public bool $withType = false;
	public $showOnEmpty = false;
	public $emptyText = false;
	public $summary = false;
	public $responsive = false;
	public bool $archiveBtn = false;

	public function init(): void {
		if ($this->dataProvider === null) {
			if ($this->model === null) {
				throw new InvalidConfigException('$model must be set when dataProvider is not set.');
			}
			$this->dataProvider = new ArrayDataProvider([
				'allModels' => $this->getModels(),
				'sort' => [
					'attributes' => [
						'date_at',
						'typeName',
					],
					'defaultOrder' => [
						'date_at' => SORT_DESC,
					],
				],
			]);
		}
		$this->caption = Yii::t('lead', 'Same Contacts Leads');
		if ($this->archiveBtn) {
			$this->caption .= ArchiveSameContactButton::widget(['model' => $this->model]);
		}
		if (empty($this->columns)) {
			$this->columns = $this->defaultColumns();
		}
		parent::init();
	}

	private function getModels(): array {
		$models = $this->model->getSameContacts($this->withType);
		$indexedByIDAndHash = [];
		foreach ($models as $model) {
			$indexedByIDAndHash[$model->getId() . ':' . $model->getHash()] = $model;
		}
		return $indexedByIDAndHash;
	}

	protected function defaultColumns(): array {
		return [
			[
				'class' => ExpandRowColumn::class,
				'detailUrl' => Url::toRoute(['report/reports-details']),
				'value' => function (): int {
					return GridView::ROW_COLLAPSED;
				},
			],
			[
				'attribute' => 'typeName',
				'noWrap' => true,
			],
			[
				'attribute' => 'statusName',
				'contentBold' => true,
				'noWrap' => true,
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'date_at',
			],
			'sourceName',
			'owner',
			[
				'class' => ActionColumn::class,
				'template' => '{copy} {view} {update}',
				'urlCreator' => function ($action, ActiveLead $model) {
					return Url::toRoute([$action, 'id' => $model->getId()]);
				},
				'visibleButtons' => [
					'view' => function (ActiveLead $model): bool {
						return Module::getInstance()->manager->isForUser($model);
					},
					'update' => function (ActiveLead $model): bool {
						return Module::getInstance()->manager->isForUser($model);
					},
				],
				'buttons' => [
					'copy' => function ($url, ActiveLead $model): string {
						return CopyLeadBtnWidget::widget([
							'lead' => $model,
						]);
					},
				],
			],

		];
	}
}
