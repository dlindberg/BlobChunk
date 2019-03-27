<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Parser;

interface Parse
{
    public function parse(\DOMElement $node): array;
}
