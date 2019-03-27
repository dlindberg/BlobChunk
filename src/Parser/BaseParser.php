<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Parser;

use dlindberg\BlobChunk\Manager\CheckNode;
use dlindberg\DOMDocumentFactory\DOMDocumentFactory as Doc;

abstract class BaseParser implements parse
{
    /**
     * @var CheckNode
     */
    protected $manager;

    /**
     * @var Doc
     */
    public $doc;

    public function __construct(CheckNode $manager)
    {
        $this->manager = $manager;
        $this->doc = new Doc();
    }

    public function setDocFactory(Doc $factory): void
    {
        $this->doc = $factory;
    }

    protected function stringify(\DOMNode $node): string
    {
        return $this->doc->stringify($node);
    }

    protected function stringifyList(\DOMNodeList $nodes): array
    {
        return $this->doc->stringifyFromList($nodes);
    }

    protected function collect(callable $test, \DOMNode $node, \DOMDocument $carry): \DOMNodeList
    {
        if ($test($node)) {
            $carry->documentElement->appendChild($carry->importNode($node, true));
        }

        return null !== $node->nextSibling ?
            $this->collect($test, $node->nextSibling, $carry) : $carry->documentElement->childNodes;
    }

    protected function containerDOM(): \DOMDocument
    {
        $document = new \DOMDocument();
        $document->appendChild($document->createElement('body', ''));

        return $document;
    }
}
