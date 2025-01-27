<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\entity\document\DocumentInnerViewDto;
use common\modules\court\modules\spi\repository\DocumentRepository;

/**
 * @property DocumentRepository $repository
 */
class DocumentsTest extends BaseRepositoryTest {

	public $repositoryModelClass = DocumentRepository::class;

	public function testDownload(): void {
		$models = $this->repository->getDataProvider()->getModels();
		$this->tester->assertNotEmpty($models);

		codecept_debug($models);
		foreach ($models as $model) {

			$this->tester->assertInstanceOf(DocumentInnerViewDto::class, $model);
			//$this->repository->download($model->id);

		}
		$first = reset($models);
		$this->tester->assertInstanceOf(DocumentInnerViewDto::class, $first);
		$content = $this->repository->download($first->id);
		$this->tester->assertNotEmpty($content);
		$path = codecept_output_dir() . '/' . $first->fileName;
		file_put_contents($path, $content);
		$this->tester->assertFileExists($path);
	}

}
