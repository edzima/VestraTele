<?php

namespace common\tests\_support;

use common\models\SearchModel;
use yii\data\DataProviderInterface;

/**
 * Trait UnitSearchModelTrait
 *
 */
trait UnitSearchModelTrait {

	/**
	 * @todo refactor createModel() as getter and remove this property.
	 */
	private SearchModel $model;

	abstract protected function createModel(): SearchModel;

	protected function search(array $params = [], string $formName = null): DataProviderInterface {
		if ($formName === null) {
			$formName = $this->model->formName();
		}
		$params[$formName] = $params;
		return $this->model->search($params);
	}

	protected function assertTotalCount(int $count, array $params = []): void {
		$this->tester->assertSame($count, $this->search($params)->getTotalCount());
	}
}
