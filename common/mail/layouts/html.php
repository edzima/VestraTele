<?php

use common\models\KeyStorageItem;
use common\helpers\Html;
use yii\mail\MessageInterface;
use yii\web\View;

/* @var $this View view component instance */
/* @var $message MessageInterface the message being composed */
/* @var $content string main view render result */

$favicon = Yii::getAlias('@frontendUrl') . '/' . 'favicon.ico';
$background = Yii::$app->keyStorage->get(KeyStorageItem::KEY_EMAIL_BACKGROUND, '#34495e');
$logoUrl = Yii::$app->keyStorage->get(KeyStorageItem::KEY_EMAIL_LOGO_URL);
$darkBackground = Html::luminanceDark($background, 0.2);
$darkestBackground = Html::luminanceDark($background, 0.4);
$footerText = Yii::$app->keyStorage->get(KeyStorageItem::KEY_EMAIL_FOOTER_TEXT);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
	<meta charset="utf-8"> <!-- utf-8 works for most cases -->
	<meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
	<meta name="x-apple-disable-message-reformatting">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
	<meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no"> <!-- Tell iOS not to automatically link certain text strings. -->
	<meta name="color-scheme" content="light dark">
	<meta name="supported-color-schemes" content="light dark">
	<link rel="shortcut icon" href="<?= $favicon ?>">
	<title><?= Html::encode($this->title) ?></title>

	<!-- What it does: Makes background images in 72ppi Outlook render at correct size. -->
	<!--[if gte mso 9]>
	<xml>
		<o:OfficeDocumentSettings>
			<o:PixelsPerInch>96</o:PixelsPerInch>
		</o:OfficeDocumentSettings>
	</xml>
	<![endif]-->

	<!-- Web Font / @font-face : BEGIN -->
	<!-- NOTE: If web fonts are not required, lines 23 - 41 can be safely removed. -->

	<!-- Desktop Outlook chokes on web font references and defaults to Times New Roman, so we force a safe fallback font. -->
	<!--[if mso]>
	<style>
		* {
			font-family: sans-serif !important;
		}
	</style>
	<![endif]-->

	<!-- All other clients get the webfont reference; some will render the font and others will silently fail to the fallbacks. More on that here: https://web.archive.org/web/20190717120616/http://stylecampaign.com/blog/2015/02/webfont-support-in-email/ -->
	<!--[if !mso]><!-->
	<!-- insert web font reference, eg: <link href='https://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'> -->
	<!--<![endif]-->

	<!-- Web Font / @font-face : END -->

	<!-- CSS Reset : BEGIN -->
	<style>

		/* What it does: Tells the email client that both light and dark styles are provided. A duplicate of meta color-scheme meta tag above. */
		:root {
			color-scheme: light dark;
			supported-color-schemes: light dark;
		}

		/* What it does: Remove spaces around the email design added by some email clients. */
		/* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
		html,
		body {
			margin: 0 auto !important;
			padding: 0 !important;
			height: 100% !important;
			width: 100% !important;
		}

		/* What it does: Stops email clients resizing small text. */
		* {
			-ms-text-size-adjust: 100%;
			-webkit-text-size-adjust: 100%;
		}

		/* What it does: Centers email on Android 4.4 */
		div[style*="margin: 16px 0"] {
			margin: 0 !important;
		}

		/* What it does: forces Samsung Android mail clients to use the entire viewport */
		#MessageViewBody, #MessageWebViewDiv {
			width: 100% !important;
		}

		/* What it does: Stops Outlook from adding extra spacing to tables. */
		table,
		td {
			mso-table-lspace: 0pt !important;
			mso-table-rspace: 0pt !important;
		}

		/* What it does: Fixes webkit padding issue. */
		table {
			border-spacing: 0 !important;
			border-collapse: collapse !important;
			table-layout: fixed !important;
			margin: 0 auto !important;
		}

		/* What it does: Uses a better rendering method when resizing images in IE. */
		img {
			-ms-interpolation-mode: bicubic;
		}

		/* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
		a {
			text-decoration: none;
		}

		/* What it does: A work-around for email clients meddling in triggered links. */
		a[x-apple-data-detectors], /* iOS */
		.unstyle-auto-detected-links a,
		.aBn {
			border-bottom: 0 !important;
			cursor: default !important;
			color: inherit !important;
			text-decoration: none !important;
			font-size: inherit !important;
			font-family: inherit !important;
			font-weight: inherit !important;
			line-height: inherit !important;
		}

		/* What it does: Prevents Gmail from changing the text color in conversation threads. */
		.im {
			color: inherit !important;
		}

		/* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */
		.a6S {
			display: none !important;
			opacity: 0.01 !important;
		}

		/* If the above doesn't work, add a .g-img class to any image in question. */
		img.g-img + div {
			display: none !important;
		}

		/* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
		/* Create one of these media queries for each additional viewport size you'd like to fix */

		/* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
		@media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
			u ~ div .email-container {
				min-width: 320px !important;
			}
		}

		/* iPhone 6, 6S, 7, 8, and X */
		@media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
			u ~ div .email-container {
				min-width: 375px !important;
			}
		}

		/* iPhone 6+, 7+, and 8+ */
		@media only screen and (min-device-width: 414px) {
			u ~ div .email-container {
				min-width: 414px !important;
			}
		}

	</style>
	<!-- CSS Reset : END -->

	<!-- Progressive Enhancements : BEGIN -->
	<style>

		.main-content-container a {
			color: <?= $darkBackground?>;
		}

		/* What it does: Hover styles for buttons */
		.button-td,
		.button-a {
			transition: all 100ms ease-in;
		}

		.button-td-primary:hover,
		.button-a-primary:hover {
			background: #555555 !important;
			border-color: #555555 !important;
		}

		/* Media Queries */
		@media screen and (max-width: 480px) {

			/* What it does: Forces table cells into full-width rows. */
			.stack-column,
			.stack-column-center {
				display: block !important;
				width: 100% !important;
				max-width: 100% !important;
				direction: ltr !important;
			}

			/* And center justify these ones. */
			.stack-column-center {
				text-align: center !important;
			}

			/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */
			.center-on-narrow {
				text-align: center !important;
				display: block !important;
				margin-left: auto !important;
				margin-right: auto !important;
				float: none !important;
			}

			table.center-on-narrow {
				display: inline-block !important;
			}

			/* What it does: Adjust typography on small screens to improve readability */
			.email-container p {
				font-size: 17px !important;
			}
		}

		/* Dark Mode Styles : BEGIN */
		@media (prefers-color-scheme: dark) {
			.email-bg {
				background: #111111 !important;
			}

			.darkmode-bg-logo {
				background: #F2f2f2 !important;
			}

			.darkmode-bg {
				background: #222222 !important;
			}


			h1,
			h2,
			h3,
			p,
			li,
			.darkmode-text,
			.email-container a:not([class]) {
				color: #F7F7F9 !important;
			}

			/*td.button-td-primary,*/
			/*td.button-td-primary a {*/
			/*	background-color: #ffffff !important;*/
			/*	border-color: #ffffff !important;*/
			/*	color: #222222 !important;*/
			/*}*/
			/*td.button-td-primary:hover,*/
			/*td.button-td-primary a:hover {*/
			/*	background-color: #cccccc !important;*/
			/*	border-color: #cccccc !important;*/
			/*}*/
			.footer td {
				color: #aaaaaa !important;
			}

			.email-container .main-content-container a:not([class]) {
				color: <?= $darkBackground ?> !important;
			}

			.darkmode-fullbleed-bg {
				background-color: <?= $darkestBackground ?> !important;
			}
		}

		/* Dark Mode Styles : END */
	</style>
	<!-- Progressive Enhancements : END -->
	<?php $this->head() ?>
</head>
<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: <?= $background ?>;" class="email-bg">
<center role="article" aria-roledescription="email" lang="en" style="width: 100%; background-color: <?= $background ?>;" class="email-bg">
	<!--[if mso | IE]>
	<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: <?= $background ?>;" class="email-bg">
		<tr>
			<td>
	<![endif]-->


	<?php if (isset($this->params['preheaderText'])): ?>

		<!-- Visually Hidden Preheader Text : BEGIN -->
		<div style="max-height:0; overflow:hidden; mso-hide:all;" aria-hidden="true">
			<?= Html::encode($this->params['preheaderText']) ?>
		</div>
		<!-- Visually Hidden Preheader Text : END -->
	<?php endif; ?>


	<!-- Create white space after the desired preview text so email clients don’t pull other distracting text into the inbox preview. Extend as necessary. -->
	<!-- Preview Text Spacing Hack : BEGIN -->
	<div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
		&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
	</div>
	<!-- Preview Text Spacing Hack : END -->

	<!--
		Set the email width. Defined in two places:
		1. max-width for all clients except Desktop Windows Outlook, allowing the email to squish on narrow but never go wider than 680px.
		2. MSO tags for Desktop Windows Outlook enforce a 680px width.
		Note: The Fluid and Responsive templates have a different width (600px). The hybrid grid is more "fragile", and I've found that 680px is a good width. Change with caution.
	-->
	<div style="max-width: 680px; margin: 0 auto;" class="email-container">
		<!--[if mso]>
		<table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="680">
			<tr>
				<td>
		<![endif]-->

		<!-- Email Body : BEGIN -->
		<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">

			<?php if ($logoUrl): ?>
				<!-- Email Header : BEGIN -->
				<tr>
					<td style="padding: 20px 0; text-align: center; background-color:#FFFFFF;" class="darkmode-bg-logo">
						<img src="<?= $logoUrl ?> " width="208" height="54" alt="<?= Yii::$app->name ?>" border="0" style="height: auto; font-family: sans-serif; font-size: 15px; line-height: 15px; color: #555555;">
					</td>
				</tr>
				<!-- Email Header : END -->

			<?php endif; ?>

			<!-- Clear Spacer : BEGIN -->
			<tr>
				<td aria-hidden="true" height="4" style="font-size: 0px; line-height: 0px; background-color:<?= $background ?>">
					&nbsp;
				</td>
			</tr>
			<!-- Clear Spacer : END -->

			<!-- 1 Column Text + Button : BEGIN -->
			<tr>
				<td style="background-color: #ffffff;" class="darkmode-bg">
					<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
						<tr>
							<td class="main-content-container" style="padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
								<h1 style="margin: 0 0 10px; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;"><?= Html::encode($this->title) ?></h1>
								<div style="margin: 0 0 10px;"><?= $content ?></div>
							</td>
						</tr>
						<?php if (isset($this->params['primaryButtonText']) && isset($this->params['primaryButtonHref'])): ?>

							<tr>
								<td style="padding: 0 20px 20px;">
									<!-- Button : BEGIN -->
									<table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: auto;">
										<tr>
											<td class="button-td button-td-primary" style="border-radius: 4px; background: <?= $background ?>;">
												<a
														class="button-a button-a-primary"
														href="<?= $this->params['primaryButtonHref'] ?>"
														style="background: <?= $background ?>; border: 1px solid <?= $background ?>; font-family: sans-serif; font-size: 15px; line-height: 15px; text-decoration: none; padding: 13px 17px; color: #ffffff; display: block; border-radius: 4px;"
												><?= $this->params['primaryButtonText'] ?>
												</a>
											</td>
										</tr>
									</table>
									<!-- Button : END -->
								</td>
							</tr>
						<?php endif; ?>

					</table>
				</td>
			</tr>
			<!-- 1 Column Text + Button : END -->


		</table>
		<!-- Email Body : END -->

		<?php if ($footerText): ?>
			<!-- Email Footer : BEGIN -->
			<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 680px;" class="footer">
				<tr>
					<td style="padding: 40px; font-family: sans-serif; font-size: 12px; line-height: 15px; text-align: center; color: #ffffff;">
						<?= $footerText ?>
					</td>
				</tr>
			</table>
			<!-- Email Footer : END -->
		<?php endif; ?>
		<!--[if mso]>
		</td>
		</tr>
		</table>
		<![endif]-->
	</div>

	<!-- Full Bleed Background Section : BEGIN -->
	<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: <?= $darkBackground ?>;" class="darkmode-fullbleed-bg">
		<tr>
			<td>
				<div align="center" style="max-width: 680px; margin: auto;" class="email-container">
					<!--[if mso]>
					<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="680" align="center">
						<tr>
							<td>
					<![endif]-->
					<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
						<tr>
							<td style="padding: 20px; text-align: left; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #ffffff;">
								<p style="margin: 0;">    <?= Yii::t('common', 'email.bleed-background-text') ?></p>
							</td>
						</tr>
					</table>
					<!--[if mso]>
					</td>
					</tr>
					</table>
					<![endif]-->
				</div>
			</td>
		</tr>
	</table>
	<!-- Full Bleed Background Section : END -->

	<!--[if mso | IE]>
	</td>
	</tr>
	</table>
	<![endif]-->
</center>
</body>


</html>
<?php $this->endPage() ?>
