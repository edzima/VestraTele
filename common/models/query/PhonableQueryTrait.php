<?php

namespace common\models\query;

use yii\db\Expression;

trait PhonableQueryTrait {

	protected function getPhoneColumns(): array {
		return [
			'phone',
		];
	}

	public function withPhoneNumber($value): self {
		[$table, $alias] = $this->getTableNameAndAlias();
		$i = 0;
		foreach ($this->getPhoneColumns() as $phoneColumn) {
			$phoneColumn = "$alias.$phoneColumn";
			if ($i === 0) {
				if (empty($value)) {
					$this->andWhere([$phoneColumn => null]);
				} else {
					$this->andWhere(['like', $this->preparePhoneExpression($phoneColumn), $value]);
				}
			} else {
				if (empty($value)) {
					$this->andWhere([$phoneColumn => null]);
				} else {
					$this->orWhere(['like', $this->preparePhoneExpression($phoneColumn), $value]);
				}
			}
			$i++;
		}
		return $this;
	}

	protected function preparePhoneExpression(string $phoneColumn): Expression {
		return new Expression(
			"REPLACE( REPLACE($phoneColumn, ' ', '') , '-', '')"
		);
	}
}
