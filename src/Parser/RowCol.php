<?php

namespace dlindberg\BlobChunk\Parser;

final class RowCol extends BaseParser implements Parse
{
    public function parse(\DOMElement $node): array
    {
        $this->manager->setRowColParentNode($node);
        $thead = $this->manager->rowColHasHeaders() ?
            $this->stringifyList($this->collectRowColDefs($node->firstChild)) : [];
        $tbody = $this->manager->rowColHasHeaders() ?
            $this->collectRowColRowGroups($node->firstChild) : [];
        if (\method_exists($tbody, 'count')) {
            $tbody = 1 === $tbody->count() ?
                $this->stringifyList($this->collectRows($tbody->item(0)->firstChild)) : $this->stringifyList($tbody);
        }
        $tbody = 0 === \count($tbody) ? $this->stringifyList($this->collectRows($node->firstChild)) : $tbody;
        $thead = 0 === \count($thead) ? \array_shift($tbody) : $thead;

        return \array_map(function ($row) use ($thead) {
            return [
                'thead' => $thead,
                'tr'    => $row,
            ];
        }, $tbody);
    }

    private function collectRowColDefs(\DOMNode $node): \DOMNodeList
    {
        return $this->collect([$this->manager, 'isRowColHeaderSectionNode'], $node, $this->containerDOM());
    }

    private function collectRowColRowGroups(\DOMNode $node): \DOMNodeList
    {
        return $this->collect([$this->manager, 'isRowColRowGroupNode'], $node, $this->containerDOM());
    }

    private function collectRows(\DOMNode $node): \DOMNodeList
    {
        return $this->collect([$this->manager, 'isRowColRowNode'], $node, $this->containerDOM());
    }
}
