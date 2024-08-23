<?php
/* This file is under Git Control by KDSI. */

require_once(DIR_SYSTEM . 'library/shared/CssToInlineStyles/vendor/autoload.php');

use TijsVerkoyen\CssToInlineStyles\Css\Processor;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class EmailTemplate_CssToInlineStyles extends CssToInlineStyles {
	/**
	 * @param string $html
	 * @return \DOMDocument
	 */
	protected function createDomDocumentFromHtml($html)
	{
		$document = new \DOMDocument('1.0', 'UTF-8');
		$internalErrors = libxml_use_internal_errors(true);
		$document->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
		libxml_use_internal_errors($internalErrors);
		$document->formatOutput = true;

		return $document;
	}

	/**
	 * Will inline the $css into the given $html
	 *
	 * Remark: ignore <style>-tags if passed css
	 *
	 * @param string $html
	 * @param string $css
	 * @return string
	 */
	public function convert($html, $css = null)
	{
		$document = $this->createDomDocumentFromHtml($html);
		$processor = new Processor();

		if ($css !== null) {
			$rules = $processor->getRules($css);
		} else {
			// get all styles from the style-tags
			$rules = $processor->getRules(
				$processor->getCssFromStyleTags($html)
			);
		}

		$document = $this->inline($document, $rules);

		return htmlspecialchars_decode($this->getHtmlFromDocument($document));
	}
}