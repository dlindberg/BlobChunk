<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Parser;

final class RecursiveNode extends BaseParser implements Parse
{
    public function parse(\DOMElement $node): array
    {
        return $this->collectRecursiveChildren($node->firstChild);
    }

    private function collectRecursiveChildren(?\DOMNode $node, array $nodes = []): array
    {
        if ($node instanceof \DOMElement && $node->hasChildNodes()) {
            ['parent' => $parent, 'children' => $children] = $this->collectRecursions($node);
            $nodes[] = $children ? [$parent, $children] : $parent;
        }

        return null !== $node->nextSibling ? $this->collectRecursiveChildren($node->nextSibling, $nodes) : $nodes;
    }

    private function collectRecursions(\DOMElement $node): array
    {
        $children = $this->findRecursionParent($node->firstChild);
        if ($children instanceof \DOMElement) {
            $node->removeChild($children);
            $children = $this->parse($children);
        }

        return ['parent' => $this->stringify($node), 'children' => $children];
    }

    private function findRecursionParent(\DOMNode $node): ?\DOMElement
    {
        if ($node instanceof \DOMElement && $this->manager->isRecursiveParentNode($node)) {
            return $node;
        } elseif (null !== $node->nextSibling) {
            return $this->findRecursionParent($node->nextSibling);
        } else {
            return null;
        }
    }
}
