<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Dumper;

use Symfony\Component\Translation\MessageCatalogue;

/**
 * XliffFileDumper generates xliff files from a message catalogue.
 *
 * @author Michel Salib <michelsalib@hotmail.com>
 */
class XliffFileDumper extends FileDumper
{
		/**
		 * {@inheritDoc}
		 */
		protected function format(MessageCatalogue $messages, $domain)
		{
				$dom = new \DOMDocument('1.0', 'utf-8');
				$dom->formatOutput = true;
				$xliff = $dom->appendChild($dom->createElement('xliff'));
				$xliff->setAttribute('version', '1.2');
				$xliff->setAttribute('xmlns', 'urn:oasis:names:tc:xliff:document:1.2');
				$xliffFile = $xliff->appendChild($dom->createElement('file'));
				$xliffFile->setAttribute('source-language', $messages->getLocale());
				$xliffFile->setAttribute('datatype', 'plaintext');
				$xliffFile->setAttribute('original', 'file.ext');
				$xliffBody = $xliffFile->appendChild($dom->createElement('body'));
				$id = 1;
				foreach ($messages->all($domain) as $source => $target) {
						$trans = $dom->createElement('trans-unit');
						$trans->setAttribute('id', $id);
						$s = $trans->appendChild($dom->createElement('source'));
						$s->appendChild($dom->createTextNode($source));
						$t = $trans->appendChild($dom->createElement('target'));
						$t->appendChild($dom->createTextNode($target));
						$xliffBody->appendChild($trans);
						$id++;
				}

				return $dom->saveXML();
		}

		/**
		 * {@inheritDoc}
		 */
		protected function getExtension()
		{
				return 'xlf';
		}
}
