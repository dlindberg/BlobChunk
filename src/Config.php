<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk;

use dlindberg\BlobChunk\Manager\CheckNode;
use dlindberg\BlobChunk\Manager\Manager;
use dlindberg\BlobChunk\Manager\NodeCheck;
use dlindberg\BlobChunk\Parser\Parse;
use dlindberg\BlobChunk\Parser\Parser;
use dlindberg\DOMDocumentFactory\DOMDocumentFactory;
use dlindberg\BlobChunk\Manager\Config as ManagerConfig;

class Config
{
    private static $default = [
        'parents'    => ['tags' => ['div'],],
        'specials'   => ['tags' => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'strong', 'em', 'b', 'i', 'a'],],
        'rowCol'     => [
            [
                'parent'    => ['type' => 'tag', 'value' => 'table',],
                'colDefs'   => ['type' => 'tag', 'value' => 'thead',],
                'rowsGroup' => ['type' => 'tag', 'value' => 'tbody',],
                'row'       => ['type' => 'tag', 'value' => 'tr',],
            ],
        ],
        'recursive'  => ['parents' => ['tags' => ['ul', 'ol',]], 'children' => ['tags' => ['li',],],],
        'pairs'      => [
            [
                'parent' => ['type' => 'tag', 'value' => 'dl',],
                'a'      => ['type' => 'tag', 'value' => 'dt',],
                'b'      => ['type' => 'tag', 'value' => 'dd',],
            ],
        ],
        'splits'     => ['what' => ['tags' => ['p'],], 'on' => ['. ', '? ', '! ']],
        'docFactory' => null,
        'manager'    => null,
        'parser'     => null,
    ];

    /**
     * @var array
     */
    private $config;
    /**
     * @var ManagerConfig
     */
    private $managerConfig;

    /**
     * @var CheckNode
     */
    public $manager;

    /**
     * @var Parse
     */
    public $parser;

    /**
     * @var DOMDocumentFactory
     */
    public $docFactory;


    private function __construct(array $config)
    {
        $this->config = $config;
        $this->setDocFactory($this->getKeyObject('docFactory'));
        $this->setManager($this->getKeyObject('manager'));
        $this->setParser($this->getKeyObject('Parser'));
    }

    public static function createConfig(array $config = null): Config
    {
        return new Config(\is_array($config) ? $config : self::$default);
    }

    public function setManagerConfig(): void
    {
        $this->managerConfig = new ManagerConfig($this->getManagerConfigArray());
    }

    public function getManagerConfig(): ManagerConfig
    {
        $this->setManagerConfig();

        return $this->managerConfig;
    }

    public function setManager(?object $manager = null): void
    {
        $this->manager = $manager instanceof NodeCheck ? $manager : new Manager($this->getManagerConfig());
    }

    public function setDocFactory(?object $factory = null): void
    {
        $this->docFactory = $factory instanceof DOMDocumentFactory ? $factory : new DOMDocumentFactory();
    }

    public function setParser(?object $parser = null): void
    {
        $this->parser = $parser instanceof Parse ? $parser : new Parser($this->manager);
        if (\method_exists($this->parser, 'setDocFactory')) {
            $this->parser->setDocFactory($this->docFactory);
        }
    }

    private function getKeyArray(string $key): array
    {
        return $this->hasKey($key) && \is_array($this->config[$key]) ? $this->config[$key] : [];
    }

    private function getKeyObject(string $key): ?object
    {
        return $this->haskey($key) && \is_object($this->config[$key]) ? $this->config[$key] : null;
    }

    private function hasKey(string $key): bool
    {
        return \array_key_exists($key, $this->config);
    }

    private function getManagerConfigArray(): array
    {
        return [
            \array_merge(['group' => 'parents',], $this->getKeyArray('parents')),
            \array_merge(['group' => 'specials',], $this->getKeyArray('specials')),
            \array_merge(['group' => 'rowCol',], $this->getKeyArray('rowCol')),
            \array_merge(['group' => 'recursive',], $this->getKeyArray('recursive')),
            \array_merge(['group' => 'pairs',], $this->getKeyArray('pairs')),
            \array_merge(['group' => 'splits',], $this->getKeyArray('splits')),
        ];
    }
}
