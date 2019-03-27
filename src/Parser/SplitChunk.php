<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Parser;

final class SplitChunk extends BaseParser implements Parse
{

    public function parse(\DOMElement $input): array
    {
        $strings[] = $this->stringify($input);

        return $this->blowItUp($this->manager->getSplitNodeDelimiters(), $strings);
    }

    private function blowItUp(array $delimiters, array $strings): array
    {
        $delimiter = \array_shift($delimiters);
        $strings = \array_merge(...\array_map(function (string $string) use ($delimiter): array {
            return $this->doSplit($delimiter, $string);
        }, $strings));

        return 0 === \count($delimiters) ? $strings : $this->blowItUp($delimiters, $strings);
    }

    private function doSplit(string $delimiter, string $string): array
    {
        return \array_map(function ($part) use ($delimiter): string {
            return !\in_array(substr($part, -1), \array_merge(['>'], $this->manager->getSplitNodeDelimiters(true))) ?
                $part . \trim($delimiter) : $part;
        }, \explode($delimiter, $string));
    }
}
