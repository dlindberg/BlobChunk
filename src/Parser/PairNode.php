<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Parser;

final class PairNode extends BaseParser implements Parse
{
    public function parse(\DOMElement $node): array
    {
        $this->manager->setPairParentNode($node);

        return $this->groupPairNodes($node->firstChild);
    }

    private function groupPairNodes(\DOMNode $node, $carry = []): ?array
    {
        if ($this->manager->isPairSideANode($node)) {
            $a = $this->stringify($node);
            ['bNodes' => $b, 'currentNode' => $node] = $this->collectBNodes($node->nextSibling);
            $carry[] = [
                'a' => $a,
                'b' => $b,
            ];
        }

        return null !== $node->nextSibling ? $this->groupPairNodes($node->nextSibling, $carry) : $carry;
    }

    private function collectBNodes(?\DOMNode $node, array $nodes = []): array
    {
        if ($this->manager->isPairSideBNode($node)) {
            $nodes[] = $this->stringify($node);
        }
        if ($this->manager->isPairSideANode($node)) {
            $node = $node->previousSibling;
        } elseif (null !== $node->nextSibling) {
            return $this->collectBNodes($node->nextSibling, $nodes);
        }

        return [
            'currentNode' => $node,
            'bNodes'      => $nodes,
        ];
    }
}
