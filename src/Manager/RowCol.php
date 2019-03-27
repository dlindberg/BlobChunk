<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Manager;

final class RowCol extends NodeCheck
{
    public $colDefs   = [];
    public $rowsGroup = [];
    public $row       = [];

    public function getSet(\DOMNode $parent): RowCol
    {
        $set = $this->get($parent, $this->config->rowColSets);
        $this->colDefs = $set['colDefs'] ?? [];
        $this->rowsGroup = $set['rowsGroup'] ?? [];
        $this->row = $set['row'];

        return $this;
    }
}
