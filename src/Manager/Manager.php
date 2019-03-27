<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Manager;

final class Manager extends NodeCheck implements CheckNode
{
    /**
     * @var RowCol
     */
    private $rowCol;

    /**
     * @var Pairs
     */
    private $pairs;

    /**
     * @var Pairs|null
     */
    private $currentPair;

    /**
     * @var RowCol|null
     */
    private $currentRowCol;

    public function __construct(Config $config)
    {
        parent::__construct($config);
        $this->rowCol = new RowCol($config);
        $this->pairs = new Pairs($config);
    }

    public function getType(\DOMNode $input): string
    {
        return $this->nameNodeType($input);
    }

    public function isParentNode(\DOMNode $input): bool
    {
        return $this->test($input, 'parent');
    }

    public function isSpecialNode(\DOMNode $input): bool
    {
        return $this->test($input, 'special');
    }

    public function isSplitNode(\DOMNode $input): bool
    {
        return $this->test($input, 'splitWhat');
    }

    public function getSplitNodeDelimiters(bool $trim = false): array
    {
        return $trim ? $this->trimmedDelimiters() : $this->config->splitOn;
    }

    public function isRecursiveParentNode(\DOMNode $input): bool
    {
        return $this->test($input, 'recursionParent');
    }

    public function isRecursiveChildNode(\DOMNode $input): bool
    {
        return $this->test($input, 'recursionChildren');
    }

    public function isPairParentNode(\DOMNode $input): bool
    {
        return $this->test($input, 'pairParents');
    }

    public function setPairParentNode(\DOMNode $input): ?CheckNode
    {
        $this->currentPair = $this->isPairParentNode($input) ? $this->pairs->getSet($input) : null;

        return $this->currentPair ? $this : null;
    }

    public function clearPairParentNode(): void
    {
        $this->currentPair = null;
    }

    public function isPairSideANode(\DOMNode $input): bool
    {
        return $this->currentPair ? $this->testFlat($input, $this->currentPair->sideA) : false;
    }

    public function isPairSideBNode(\DOMNode $input): bool
    {
        return $this->currentPair ? $this->testFlat($input, $this->currentPair->sideB) : false;
    }

    public function isRowColParentNode(\DOMNode $input): bool
    {
        return $this->test($input, 'rowColParent');
    }

    public function setRowColParentNode(\DOMNode $input): ?CheckNode
    {
        $this->currentRowCol = $this->isRowColParentNode($input) ? $this->rowCol->getSet($input) : null;

        return $this->currentRowCol ? $this : null;
    }

    public function clearRowColParentNode(): void
    {
        $this->currentRowCol = null;
    }

    public function rowColHasHeaders(): bool
    {
        return $this->currentRowCol &&
               \array_key_exists('value', $this->currentRowCol->colDefs) &&
               \array_key_exists('value', $this->currentRowCol->rowsGroup);
    }

    public function isRowColHeaderSectionNode(\DOMNode $input): bool
    {
        return $this->currentRowCol ? $this->testFlat($input, $this->currentRowCol->colDefs) : false;
    }

    public function isRowColRowGroupNode(\DOMNode $input): bool
    {
        return $this->currentRowCol ? $this->testFlat($input, $this->currentRowCol->rowsGroup) : false;
    }

    public function isRowColRowNode(\DOMNode $input): bool
    {
        return $this->currentRowCol ? $this->testFlat($input, $this->currentRowCol->row) : false;
    }
}
