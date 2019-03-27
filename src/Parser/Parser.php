<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Parser;

use dlindberg\DOMDocumentFactory\DOMDocumentFactory as Doc;
use dlindberg\BlobChunk\Manager\CheckNode;

final class Parser extends BaseParser implements Parse
{
    /**
     * @var Parse
     */
    public $pairNode;
    /**
     * @var Parse
     */
    public $recursiveNode;
    /**
     * @var Parse
     */
    public $rowCol;
    /**
     * @var Parse
     */
    public $splitChunk;

    public function __construct(CheckNode $manager)
    {
        parent::__construct($manager);
        $this->pairNode = new PairNode($manager);
        $this->recursiveNode = new RecursiveNode($manager);
        $this->rowCol = new RowCol($manager);
        $this->splitChunk = new SplitChunk($manager);
    }

    public function setDocFactory(Doc $factory): void
    {
        parent::setDocFactory($factory);
        $this->pairNode->setDocFactory($factory);
        $this->recursiveNode->setDocFactory($factory);
        $this->rowCol->setDocFactory($factory);
        $this->splitChunk->setDocFactory($factory);
    }

    public function parse(\DOMElement $node): array
    {
        switch ($this->manager->getType($node)) {
            case ('split'):
                $content = $this->splitChunk->parse($node);
                break;
            case ('recursive'):
                $content = $this->recursiveNode->parse($node);
                break;
            case ('rowCol'):
                $this->manager->clearRowColParentNode();
                $content = $this->rowCol->parse($node);
                break;
            case ('pairs'):
                $this->manager->clearPairParentNode();
                $content = $this->pairNode->parse($node);
                break;
            case ('special'):
            default:
                $content = $this->stringify($node);
                break;
        }

        return [
            'tag'     => $node->nodeName,
            'type'    => $this->manager->getType($node),
            'content' => $content,
        ];
    }
}
