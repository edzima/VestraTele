<?php

use common\helpers\Html;
use common\modules\credit\models\CreditClientAnalyze;
use yii\web\View;

/* @var $this View */
/* @var $model CreditClientAnalyze */

?>
<style>
	td, th {
		padding: 5px;
	}

	th {
		text-transform: uppercase;
	}

	th {
		text-align: left;
		width: 20em;
		min-width: 20em;
		max-width: 20em;
		word-break: break-all;
	}

	.title {
		font-size: 1.5rem;
	}
</style>


<div class="pdf-content">
	<!--	<table width="100%" class="table">-->
	<!--		<tr>-->
	<!--			<td>-->
	<!--			<th align="center">-->
	<!--				<h3><strong>WSTĘPNY WYNIK ANALIZY UMOWY KREDYTOWEJ/POŻYCZKI</strong></h3>-->
	<!--			</th>-->
	<!--			</td>-->
	<!---->
	<!--		</tr>-->
	<!--	</table>-->
	<h3 class="title" align="center"><strong>WSTĘPNY WYNIK ANALIZY UMOWY KREDYTOWEJ/POŻYCZKI</strong></h3>
	<table width="100%" style="margin-top:50px; font-size: 11px;page-break-inside:avoid;" class="table">
		<!--		<colgroup>-->
		<!--			<col span="1" style="width: 5%;">-->
		<!--			<col span="1" style="width: 70%;">-->
		<!--		</colgroup>-->
		<tbody>

		<tr>
			<?= Html::tag('th', $model->getAttributeLabel('borrower'), [
				'class' => 'bold-text',
			]) ?>
			<?= Html::tag('td', Html::encode($model->borrower)) ?>
		</tr>
		<tr>
			<?= Html::tag('th', $model->getAttributeLabel('entityResponsibleId'), [
				'class' => 'bold-text',
			]) ?>
			<?= Html::tag('td', Html::encode($model->getEntityResponsibleName())) ?>
		</tr>
		<tr>
			<?= Html::tag('th', $model->getAttributeLabel('agreement'), [
				'class' => 'bold-text',
			]) ?>
			<?= Html::tag('td', Html::encode($model->agreement)) ?>
		</tr>
		<tr>
			<?= Html::tag('th', $model->getAttributeLabel('agreementAt'), [
				'class' => 'bold-text',
			]) ?>
			<?= Html::tag('td', Yii::$app->formatter->asDate($model->agreementAt)) ?>
		</tr>
		<tr>
			<?= Html::tag('th', $model->getAttributeLabel('repaymentAt'), [
				'class' => 'bold-text',
			]) ?>
			<?= Html::tag('td', Yii::$app->formatter->asDate($model->repaymentAt)) ?>
		</tr>
		<tr>
			<?= Html::tag('th', $model->getAttributeLabel('totalLoanAmount'), [
				'class' => 'bold-text',
			]) ?>
			<?= Html::tag('td', Yii::$app->formatter->asCurrency($model->totalLoanAmount)) ?>
		</tr>
		<tr>
			<?= Html::tag('th', $model->getAttributeLabel('amountOfLoanGranted'), [
				'class' => 'bold-text',
			]) ?>
			<?= Html::tag('td', Yii::$app->formatter->asCurrency($model->amountOfLoanGranted)) ?>
		</tr>
		<tr>
			<?= Html::tag('th', $model->getAttributeLabel('analyzeResult'), [
				'class' => 'bold-text',
			]) ?>
			<?= Html::tag('td', Html::encode($model->analyzeResult)) ?>
		</tr>
		<tr>
			<?= Html::tag('th', $model->getAttributeLabel('estimatedRefundAmount'), [
				'class' => 'bold-text',
			]) ?>
			<?= Html::tag('td', Html::encode($model->estimatedRefundAmount)) ?>
		</tr>
		<?php if ($model->amountOfCanceledInterestOnFuture): ?>
			<tr>
				<?= Html::tag('th', $model->getAttributeLabel('amountOfCanceledInterestOnFuture'), [
					'class' => 'bold-text',
				]) ?>
				<?= Html::tag('td', Yii::$app->formatter->asCurrency($model->amountOfCanceledInterestOnFuture)) ?>
			</tr>
		<?php endif; ?>
		<tr>
			<?= Html::tag('th', $model->getAttributeLabel('analyzeAt'), [
				'class' => 'bold-text',
			]) ?>
			<?= Html::tag('td', Yii::$app->formatter->asDate($model->analyzeAt)) ?>
		</tr>
		</tbody>
		<tfoot>
		<tr>
			<th colspan="2">***Kalkulacja ma charakter orientacyjny, zawiera kwotę aktualnego roszczenia na dzień sporządzenia analizy. Kwoty zostały wyliczone na podstawie umowy kredytowej/pożyczki. Rzeczywista kwota zwrotu może się różnić z uwagi na zmiany stóp procentowych (wzrost lub spadek oprocentowania kredytu/pożyczki) w okresie kredytowania, sposób naliczania odsetek od kredytu/pożyczki, ewentualne zaległości w spłacie kredytu/pożyczki, dokonanie nadpłaty lub wcześniejszej spłaty kredytu/pożyczki przez klienta, naliczenie dodatkowych opłat przez bank (np. w wyniku aneksowania umowy itp.).</th>
		</tr>
		</tfoot>
	</table>

</div>
