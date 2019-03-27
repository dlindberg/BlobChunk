<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Manager;

final class Pairs extends NodeCheck
{
    public $sideA = [];
    public $sideB = [];

    public function getSet(\DOMNode $parent): Pairs
    {
        ['a' => $this->sideA, 'b' => $this->sideB] = $this->get($parent, $this->config->pairSets);

        return $this;
    }
}
