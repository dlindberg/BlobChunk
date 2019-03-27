<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Manager;

abstract class NodeCheck
{
    /**
     * @var Config
     */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    protected function nameNodeType(\DOMNode $input): string
    {
        if ($this->test($input, 'parent')) {
            return 'parent';
        }
        if ($this->test($input, 'special')) {
            return 'special';
        }
        if ($this->test($input, 'splitWhat')) {
            return 'split';
        }
        if ($this->test($input, 'recursionParent')) {
            return 'recursive';
        }
        if ($this->test($input, 'rowColParent')) {
            return 'rowCol';
        }
        if ($this->test($input, 'pairParents')) {
            return 'pairs';
        }

        return 'default';
    }

    protected function get(\DOMNode $parent, array $in): array
    {
        return \array_merge(...\array_filter($in, function ($set) use ($parent) {
            return $this->testFlat($parent, $set['parent']);
        }));
    }

    protected function test(\DOMNode $input, string $what): bool
    {
        $tags = $this->config->convertKey($what, 'tags');
        $attributes = $this->config->convertKey($what, 'attributes');

        return $this->testTag($input, $this->config->$tags) || $this->testAttribute($input, $this->config->$attributes);
    }

    protected function testFlat(\DOMNode $input, array $definition): bool
    {
        if (\array_key_exists('type', $definition)) {
            if ('tag' === $definition['type']) {
                return $definition['value'] === $input->nodeName;
            } elseif ('attribute' === $definition['type'] && $input->hasAttributes()) {
                return $this->walkAttributes($input->attributes, [$definition]);
            }
        }

        return false;
    }

    protected function trimmedDelimiters(): array
    {
        return \array_map(function (string $delimiter) {
            return trim($delimiter);
        }, $this->config->splitOn);
    }

    private function testTag(\DOMNode $node, array $tags): bool
    {
        return \in_array($node->nodeName, $tags);
    }

    private function testAttribute(\DOMNode $node, array $attributes): bool
    {
        return $node->hasAttributes() && 0 !== \count($attributes) ?
            $this->walkAttributes($node->attributes, $attributes) : false;
    }

    private function walkAttributes(\DOMNamedNodeMap $nodeMap, array $attributes): bool
    {
        $attribute = \array_shift($attributes);
        if ($this->getNodeValue($nodeMap, $attribute['name']) === \mb_strtolower($attribute['value'])) {
            return true;
        }

        return 0 !== \count($attributes) ? $this->walkAttributes($nodeMap, $attributes) : false;
    }

    private function getNodeValue(\DOMNamedNodeMap $nodeMap, string $what): ?string
    {
        return $nodeMap->getNamedItem(\mb_strtolower($what)) instanceof \DOMNode ?
            \mb_strtolower($nodeMap->getNamedItem(\mb_strtolower($what))->nodeValue) : null;
    }
}
