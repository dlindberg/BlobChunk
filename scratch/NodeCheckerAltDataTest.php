<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Manager;

use dlindberg\BlobChunk\Manager\Config as Config;
use dlindberg\DOMDocumentFactory\DOMDocumentFactory;
use dlindberg\DOMDocumentFactory\DOMDocumentFactoryConfig;

final class NodeCheckerAltDataTest extends \PHPUnit\Framework\TestCase
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
                'parent' => ['type' => 'attribute', 'name' => 'class', 'value' => 'h',],
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
        '<div>test Div</div><p>One Sentence only.</p>',
        '<h1>Sample H1</h1><h2>Sample h2</h2><h3>sample h3</h3><h4>sample h4</h4><h5>sample h5</h5><h6>sample h6</h6>',
        '<p>Sample paragraph. Second sentence</p>',
        '<ul><li>sample list</li></ul><ol><li>sample ordered item</li></ol>',
        '<p><strong>strong thing</strong> <em>emphasized</em> <b>emphasized</b> <i>emphasized</i></p>',
        '<p><a href="#">Sample Link</a></p>',
        '<div><p>Test P</p></div>',
        '<table><tr><td>test</td><td>test</td></tr></table>',
        '<dl><dt>sample term</dt><dd>sample def</dd></dl><dl>',
        '<div id="a">Test side one</div><div class="b">test side tow</div>',
        '<div id="c">Test side one</div><div class="d">test side two</div>',
        '<div id="i">Test side one</div><div class="j">test side two</div><div class="e">test e table</div>',
        '<div id="f">Test side one</div><div class="g">test side two</div><div class="h">test side three</div>',
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
        $sort = new NodeCheckChecker($this->config);
        $this->assertEquals('parent', $sort->nameGroup($this->samplesDocs[9]->firstChild));
        $this->assertEquals('parent', $sort->nameGroup($this->samplesDocs[9]->firstChild->nextSibling));
        $this->assertEquals('special', $sort->nameGroup($this->samplesDocs[10]->firstChild));
        $this->assertEquals('special', $sort->nameGroup($this->samplesDocs[10]->firstChild->nextSibling));
        $this->assertEquals('split', $sort->nameGroup($this->samplesDocs[2]->firstChild));
        $this->assertEquals('split', $sort->nameGroup($this->samplesDocs[11]->firstChild));
        $this->assertEquals('split', $sort->nameGroup($this->samplesDocs[11]->firstChild->nextSibling));
        $this->assertEquals('nest', $sort->nameGroup($this->samplesDocs[12]->firstChild));
        $this->assertEquals('nest', $sort->nameGroup($this->samplesDocs[12]->childNodes->item(1)));
        $this->assertEquals('nest', $sort->nameGroup($this->samplesDocs[12]->childNodes->item(2)));
        $this->assertEquals('default', $sort->nameGroup($this->samplesDocs[6]->firstChild));
    }

    public function testIsParent()
    {
        $sort = new NodeCheckChecker($this->config);
        $this->assertTrue($sort->isParent($this->samplesDocs[9]->firstChild));
        $this->assertTrue($sort->isParent($this->samplesDocs[9]->firstChild->nextSibling));
    }

    public function testIsSpecial()
    {
        $sort = new NodeCheckChecker($this->config);
        $this->assertTrue($sort->isSpecial($this->samplesDocs[10]->childNodes->item(0)));
        $this->assertTrue($sort->isSpecial($this->samplesDocs[10]->childNodes->item(1)));
        $this->assertFalse($sort->isSpecial($this->samplesDocs[9]->childNodes->item(0)));
        $this->assertFalse($sort->isSpecial($this->samplesDocs[9]->childNodes->item(1)));
    }

    public function testIsSplit()
    {
        $sort = new NodeCheckChecker($this->config);
        $this->assertTrue($sort->isSplit($this->samplesDocs[11]->childNodes->item(0)));
        $this->assertTrue($sort->isSplit($this->samplesDocs[11]->childNodes->item(1)));
        $this->assertTrue($sort->isSplit($this->samplesDocs[2]->childNodes->item(0)));
        $this->assertTrue($sort->isSplit($this->samplesDocs[6]->firstChild->firstChild));
        $this->assertFalse($sort->isSplit($this->samplesDocs[6]->firstChild));
        $this->assertFalse($sort->isSplit($this->samplesDocs[5]->firstChild->firstChild));
    }
}
