<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Manager;

interface CheckNode
{
    public function getType(\DOMNode $input): string;

    public function isParentNode(\DOMNode $input): bool;

    public function isSpecialNode(\DOMNode $input): bool;

    public function isSplitNode(\DOMNode $input): bool;

    public function getSplitNodeDelimiters(bool $trim = false): array;

    public function isRecursiveParentNode(\DOMNode $input): bool;

    public function isRecursiveChildNode(\DOMNode $input): bool;

    public function isPairParentNode(\DOMNode $input): bool;

    public function setPairParentNode(\DOMNode $input): ?CheckNode;

    public function clearPairParentNode(): void;

    public function isPairSideANode(\DOMNode $input): bool;

    public function isPairSideBNode(\DOMNode $input): bool;

    public function isRowColParentNode(\DOMNode $input): bool;

    public function setRowColParentNode(\DOMNode $input): ?CheckNode;

    public function clearRowColParentNode(): void;

    public function rowColHasHeaders(): bool;

    public function isRowColHeaderSectionNode(\DOMNode $input): bool;

    public function isRowColRowGroupNode(\DOMNode $input): bool;

    public function isRowColRowNode(\DOMNode $input): bool;
}
