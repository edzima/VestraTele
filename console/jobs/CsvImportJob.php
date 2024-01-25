<?php

namespace console\jobs;

use ruskid\csvimporter\CSVImporter;
use ruskid\csvimporter\CSVReader;
use ruskid\csvimporter\ImportInterface;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class CsvImportJob extends BaseObject implements JobInterface {

	public CSVReader $reader;
	public ImportInterface $importStrategy;

	public function execute($queue) {
		$this->run();
	}

	public function run(): ?int {
		$importer = new CSVImporter();
		$importer->setData($this->reader);
		$count = $importer->import($this->importStrategy);
		unlink($this->reader->filename);
		return $count;
	}

}
