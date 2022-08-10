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
					if (is_array($value)) {
						$this->andWhere(['REGEXP', $this->preparePhoneExpression($phoneColumn), implode('|', $value)]);
					} else {
						$this->andWhere(['like', $this->preparePhoneExpression($phoneColumn), $value]);
					}
				}
			} else {
				if (empty($value)) {
					$this->orWhere([$phoneColumn => null]);
				} else {
					if (is_array($value)) {
						$this->orWhere(['REGEXP', $this->preparePhoneExpression($phoneColumn), implode('|', $value)]);
					} else {
						$this->orWhere(['like', $this->preparePhoneExpression($phoneColumn), $value]);
					}
				}
			}
			$i++;
		}
		return $this;
	}

	protected function preparePhoneExpression(string $phoneColumn): Expression {
//		return new Expression("REGEXP_REPLACE(REPLACE($phoneColumn, ' ', '-'),
//                      '[^0-9]+',
//                      '')");
		return new Expression(
			"REPLACE( REPLACE($phoneColumn, ' ', '') , '-', '')"
		);
	}
}
