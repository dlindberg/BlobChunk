<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk;

class BlobChunk
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config = null)
    {
        $this->config = ($config instanceof Config ? $config : Config::createConfig());
    }

    public function __invoke(string $input): array
    {
        return $this->parse($input);
    }

    public function parse(string $input): array
    {
        return $this->run($this->config->docFactory->getNode($input)->firstChild);
    }

    public static function process(string $input, Config $config = null): array
    {
        return (new self($config))->parse($input);
    }

    private function run(\DOMNode $node, $carry = []): array
    {
        if ($this->config->manager->isParentNode($node)) {
            $carry = \array_merge($carry, $this->run($node->firstChild, []));
        } elseif ($node instanceof \DOMElement) {
            $carry[] = $this->config->parser->parse($node);
        }

        return null !== $node->nextSibling ?  $this->run($node->nextSibling, $carry) : $carry;
    }
}
