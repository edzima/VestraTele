<?php

namespace frontend\tests\unit\models;

use common\fixtures\helpers\HintFixtureHelper;
use common\fixtures\helpers\TerytFixtureHelper;
use common\models\hint\HintCity;
use common\models\hint\HintCitySource;
use common\tests\_support\UnitModelTrait;
use frontend\models\HintCitySourceForm;
use frontend\tests\unit\Unit;
use yii\base\Model;

class HintCitySourceFormTest extends Unit {

	use UnitModelTrait;

	private HintCitySourceForm $model;

	public function _fixtures(): array {
		return array_merge(
			TerytFixtureHelper::fixtures(),
			HintFixtureHelper::source(),
			HintFixtureHelper::user(),
			HintFixtureHelper::city()
		);
	}

	public function testEmpty(): void {
		$this->giveModel([]);
		$this->model->setHintCity($this->getHintCity());
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Source cannot be blank.', 'source_id');
		$this->thenSeeError('Status cannot be blank.', 'status');
		$this->thenSeeError('Rating cannot be blank.', 'rating');
	}

	public function testAddSource(): void {
		$this->giveModel([
			'source_id' => 1,
			'status' => HintCitySource::STATUS_NOT_ANSWER,
			'rating' => HintCitySource::RATING_POSITIVE,
			'phone' => '788-788-123',
		]);
		$this->model->setHintCity($this->getHintCity());
		$this->thenSuccessSave();
		$this->thenSeeRecord([
			'source_id' => 1,
			'status' => HintCitySource::STATUS_NOT_ANSWER,
			'rating' => HintCitySource::RATING_POSITIVE,
		]);
	}

	public function testTryDoubleSameSource(): void {
		$this->giveModel([
			'source_id' => 1,
			'status' => HintCitySource::STATUS_NOT_ANSWER,
			'rating' => HintCitySource::RATING_POSITIVE,
			'phone' => '788-788-123',
		]);
		$this->model->setHintCity($this->getHintCity());
		$this->thenSuccessSave();

		$this->giveModel([
			'source_id' => 1,
			'status' => HintCitySource::STATUS_NOT_ANSWER,
			'rating' => HintCitySource::RATING_POSITIVE,
			'phone' => '788-788-123',
		]);
		$hint = $this->getHintCity();
		$hint->refresh();
		$this->model->setHintCity($hint);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Source is invalid.', 'source_id');
	}

	private function thenSeeRecord(array $attributes): void {
		$this->tester->seeRecord(HintCitySource::class, $attributes);
	}

	private function giveModel(array $config): void {
		$this->model = new HintCitySourceForm($config);
	}

	private function getHintCity(string $index = 'new-commission'): HintCity {
		return $this->tester->grabFixture(HintFixtureHelper::CITY, $index);
	}

	public function getModel(): Model {
		return $this->model;
	}
}
