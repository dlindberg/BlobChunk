<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Manager;

use dlindberg\BlobChunk\Manager\Config as Config;
use dlindberg\BlobChunk\Manager\Nested as Nested;
use dlindberg\DOMDocumentFactory\DOMDocumentFactory;
use dlindberg\DOMDocumentFactory\DOMDocumentFactoryConfig;

final class NestedAltDataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $testData = [
        [
            'group'      => 'parents',
            'attributes' => [['name' => 'id', 'value' => "a"], ['name' => 'class', 'value' => "b"],],
        ],
        [
            'group'      => 'specials',
            'attributes' => [['name' => 'id', 'value' => 'c'], ['name' => 'class', 'value' => 'd'],],
        ],
        [
            'group' => 'rowCol',
            [
                'parent'    => ['type' => 'attribute', 'name' => 'class', 'value' => 'e',],
                'colDefs'   => ['type' => 'tag', 'value' => 'thead',],
                'rowsGroup' => ['type' => 'tag', 'value' => 'tbody',],
                'row'       => ['type' => 'tag', 'value' => 'tr',],
            ],
        ],
        [
            'group'    => 'recursive',
            'parents'  => [
                'attributes' => [
                    ['name' => 'id', 'value' => 'f'],
                    ['name' => 'class', 'value' => 'g'],
                ],
            ],
            'children' => ['tags' => ['li',],],
        ],
        [
            'group' => 'pairs',
            [
                'parent' => ['type' => 'attribute', 'name' => 'id', 'value' => 'h',],
                'a'      => ['type' => 'tag', 'value' => 'dt',],
                'b'      => ['type' => 'tag', 'value' => 'dd',],
            ],
        ],
        [
            'group' => 'splits',
            'what'  => [
                'tags'       => ['p'],
                'attributes' => [['name' => 'id', 'value' => 'i'], ['name' => 'class', 'value' => 'j'],],
            ],
            'on'    => ['. ', '? ', '! '],
        ],
    ];

    /**
     * @var Config
     */
    private $config;
    /**
     * @var DOMDocumentFactory
     */
    private $docFactory;

    /**
     * @var string[]
     */
    private $samples = [
        '<ul><li>sample list</li></ul><ol><li>sample ordered item</li></ol>',
        '<table><tr><td>test</td><td>test</td></tr></table>',
        '<dl><dt>sample term</dt><dd>sample def</dd></dl><dl>',
        '<div id="a">Test side one</div><div class="b">test side tow</div>',
        '<div id="c">Test side one</div><div class="d">test side two</div>',
        '<div id="i">Test side one</div><div class="j">test side two</div><div class="e">test e table</div>',
        '<div id="f">Test side one</div><div class="g">test side two</div><div id="h">test side three</div>',
    ];
    /**
     * @var \DOMElement[]
     */
    private $samplesDocs = [];

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->config = new Config($this->testData);
        $pureConfig = \HTMLPurifier_Config::createDefault();
        $pureConfig->set('Attr.EnableID', true);
        $this->docFactory = new DOMDocumentFactory(
            (new DOMDocumentFactoryConfig())->setInputPurifier(new \HTMLPurifier($pureConfig))
        );
        $this->samplesDocs = \array_map(function ($sample) {
            return $this->docFactory->getNode($sample);
        }, $this->samples);
    }

    public function testNameGroup()
    {
        $nested = new Nested($this->config);
        $this->assertEquals('recursive', $nested->nameGroup($this->samplesDocs[6]->firstChild));
        $this->assertEquals('rowCol', $nested->nameGroup($this->samplesDocs[5]->childNodes->item(2)));
        $this->assertEquals('pairs', $nested->nameGroup($this->samplesDocs[6]->childNodes->item(2)));
        $this->assertNull($nested->nameGroup($this->samplesDocs[0]->firstChild));
        $this->assertNull($nested->nameGroup($this->samplesDocs[1]->firstChild));
        $this->assertNull($nested->nameGroup($this->samplesDocs[2]->firstChild));
    }
}
