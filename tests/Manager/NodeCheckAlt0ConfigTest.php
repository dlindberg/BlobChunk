<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Manager;

use dlindberg\BlobChunk\Manager\Config;
use dlindberg\DOMDocumentFactory\DOMDocumentFactory;
use dlindberg\DOMDocumentFactory\DOMDocumentFactoryConfig;

final class NodeCheckAlt0ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $testData = [
        [
            'group'      => 'parents',
            'tags'       => [],
            'attributes' => [
                ['name' => 'id', 'value' => "main"],
                ['name' => 'id', 'value' => "secondary"],
            ],
        ],
        [
            'group'      => 'specials',
            'tags'       => [],
            'attributes' => [
                ['name' => 'id', 'value' => "header"],
                ['name' => 'id', 'value' => "deck"],
                ['name' => 'class', 'value' => "pull"],
                ['name' => 'class', 'value' => "bem_mode"],
                ['name' => 'class', 'value' => "bem_mode"],
            ],
        ],
        [
            'group' => 'rowCol',
            [
                'parent'    => ['type' => 'attribute', 'name' => 'class', 'value' => 'table',],
                'colDefs'   => ['type' => 'attribute', 'name' => 'class', 'value' => 'thead',],
                'rowsGroup' => ['type' => 'attribute', 'name' => 'class', 'value' => 'tbody',],
                'row'       => ['type' => 'attribute', 'name' => 'class', 'value' => 'tr',],
            ],
        ],
        [
            'group'    => 'recursive',
            'parents'  => [
                'tags'       => [],
                'attributes' => [['name' => 'class', 'value' => 'list'],['name' => 'class', 'value' => 'other']],
            ],
            'children' => [
                'tags'       => [],
                'attributes' => [['name' => 'class', 'value' => 'item']],
            ],
        ],
        [
            'group' => 'pairs',
            [
                'parent' => ['type' => 'attribute', 'name' => 'class', 'value' => 'p',],
                'a'      => ['type' => 'attribute', 'name' => 'class', 'value' => 'pa',],
                'b'      => ['type' => 'attribute', 'name' => 'class', 'value' => 'pb',],
            ],
        ],
        [
            'group' => 'splits',
            'what'  => [
                'tags'       => [],
                'attributes' => [
                    ['name' => 'class', 'value' => 'chunk'],
                    ['name' => 'id', 'value' => 'blob'],
                ],
            ],
            'on'    => ['*', '/',],
        ],
    ];
    /**
     * @var string[]
     */
    private $samples = [
        0  => '<div>test Div</div><p>One Sentence only.</p>',
        1  => '<h1>Sample H1</h1><h2>Sample h2</h2><h3>sample h3</h3>',
        2  => '<h4>sample h4</h4><h5>sample h5</h5><h6>sample h6</h6>',
        3  => '<p>Sample paragraph. Second sentence</p>',
        4  => '<ul><li>sample list</li></ul><ol><li>sample ordered item</li></ol>',
        5  => '<p><strong>strong thing</strong> <em>emphasized</em> <b>emphasized</b> <i>emphasized</i></p>',
        6  => '<p><a href="#">Sample Link</a></p>',
        7  => '<div><p>Test P</p></div>',
        8  => '<table><caption>Words</caption><tr><td>test</td><td>test</td></tr></table>',
        9  => '<dl><dt>sample term</dt><dd>sample def</dd></dl>',
        10 => '<div id="header">Test side one</div><div class="pull">test side tow</div>',
        11 => '<div id="deck">Test side one</div><div class="bem_mode">test side two</div>',
        12 => '<div id="secondary">Test side one</div><div class="j">test side two</div>',
        13 => '<div id="f">Test side one</div><div class="g">test side two</div><div class="h">test side three</div>',
        14 => '<table>
                   <thead>
                       <tr><th>test 0 0 0 </th><th>test 0 0 1</th></tr>
                   </thead>
                   <tbody>
                       <tr><td>test 0 1 0</td><td>test 0 1 1</td></tr>
                   </tbody>
               </table>',
        15 => '<p class="chunk">chunk</p><p id="blob">blob</p>',
        16 => '<div class="list"><div class="item">item</div></div>',
        17 => '<div class="other"><div class="item">item</div></div>',
        18 => '<div class="table">
                   <div class="thead">
                       <div class="tr"><div>test 0 0 0 </div><div>test 0 0 1</div></div>
                   </div>
                   <div class="tbody">
                       <div><div>test 0 1 0</div><div>test 0 1 1</div></div>
                   </div>
               </div>',
        19  => '<div class="p"><div class="pa">sample term</div><div class="pb">sample def</div></div>',
        20 => '<div id="main"><p>sample data</p></div>',
    ];
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Manager;
     */
    private $manager;
    /**
     * @var DOMDocumentFactory
     */
    private $docFactory;
    /**
     * @var \DOMElement[]
     */
    private $docs = [];

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->config = new Config($this->testData);
        $pureConfig = \HTMLPurifier_Config::createDefault();
        $pureConfig->set('Attr.EnableID', true);
        $this->docFactory = new DOMDocumentFactory(
            (new DOMDocumentFactoryConfig())->setInputPurifier(new \HTMLPurifier($pureConfig))
        );
        $this->docs = \array_map(function ($sample) {
            return $this->docFactory->getNode($sample);
        }, $this->samples);
        $this->manager = new Manager($this->config);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(Manager::class, new Manager($this->config));
    }

    public function testGetType()
    {
        $this->assertEquals('default', $this->manager->getType($this->docs[0]->firstChild));
        $this->assertEquals('default', $this->manager->getType($this->docs[1]->firstChild));
        $this->assertEquals('default', $this->manager->getType($this->docs[3]->firstChild));
        $this->assertEquals('default', $this->manager->getType($this->docs[4]->firstChild));
        $this->assertEquals('default', $this->manager->getType($this->docs[8]->firstChild));
        $this->assertEquals('default', $this->manager->getType($this->docs[9]->firstChild));
        $this->assertEquals('default', $this->manager->getType($this->docs[8]->firstChild->firstChild));
        $this->assertEquals('parent', $this->manager->getType($this->docs[20]->firstChild));
        $this->assertEquals('special', $this->manager->getType($this->docs[11]->firstChild));
        $this->assertEquals('split', $this->manager->getType($this->docs[15]->firstChild));
        $this->assertEquals('recursive', $this->manager->getType($this->docs[16]->firstChild));
        $this->assertEquals('rowCol', $this->manager->getType($this->docs[18]->firstChild));
        $this->assertEquals('pairs', $this->manager->getType($this->docs[19]->firstChild));
        $this->assertEquals('default', $this->manager->getType($this->docs[8]->firstChild->firstChild));
    }

    public function testIsParentNode()
    {
        $this->assertFalse($this->manager->isParentNode($this->docs[1]->firstChild));
        $this->assertFalse($this->manager->isParentNode($this->docs[0]->firstChild));
        $this->assertTrue($this->manager->isParentNode($this->docs[20]->firstChild));
        $this->assertTrue($this->manager->isParentNode($this->docs[12]->firstChild));
    }

    public function testIsSpecialNode()
    {
        $this->assertFalse($this->manager->isSpecialNode($this->docs[1]->childNodes->item(0)));
        $this->assertFalse($this->manager->isSpecialNode($this->docs[1]->childNodes->item(1)));
        $this->assertFalse($this->manager->isSpecialNode($this->docs[1]->childNodes->item(2)));
        $this->assertFalse($this->manager->isSpecialNode($this->docs[2]->childNodes->item(0)));
        $this->assertFalse($this->manager->isSpecialNode($this->docs[2]->childNodes->item(1)));
        $this->assertFalse($this->manager->isSpecialNode($this->docs[2]->childNodes->item(2)));
        $this->assertFalse($this->manager->isSpecialNode($this->docs[5]->firstChild->firstChild));
        $this->assertFalse($this->manager->isSpecialNode($this->docs[5]->firstChild->childNodes->item(2)));
        $this->assertFalse($this->manager->isSpecialNode($this->docs[5]->firstChild->childNodes->item(4)));
        $this->assertFalse($this->manager->isSpecialNode($this->docs[5]->firstChild->childNodes->item(6)));
        $this->assertFalse($this->manager->isSpecialNode($this->docs[6]->firstChild->firstChild));
        $this->assertFalse($this->manager->isSpecialNode($this->docs[0]->firstChild));
        $this->assertTrue($this->manager->isSpecialNode($this->docs[10]->childNodes->item(0)));
        $this->assertTrue($this->manager->isSpecialNode($this->docs[10]->childNodes->item(1)));
        $this->assertTrue($this->manager->isSpecialNode($this->docs[11]->childNodes->item(0)));
        $this->assertTrue($this->manager->isSpecialNode($this->docs[11]->childNodes->item(1)));
    }

    public function testIsSplitNode()
    {
        $this->assertFalse($this->manager->isSplitNode($this->docs[3]->firstChild));
        $this->assertFalse($this->manager->isSplitNode($this->docs[1]->firstChild));
        $this->assertTrue($this->manager->isSplitNode($this->docs[15]->firstChild));
        $this->assertTrue($this->manager->isSplitNode($this->docs[15]->childNodes->item(1)));
    }

    public function testGetSplitNodeDelimiters()
    {
        $this->assertNotEquals(['. ', '? ', '! '], $this->manager->getSplitNodeDelimiters());
        $this->assertNotEquals(['. ', '? ', '! '], $this->manager->getSplitNodeDelimiters(false));
        $this->assertNotEquals(['.', '?', '!'], $this->manager->getSplitNodeDelimiters(true));
        $this->assertEquals(['*', '/',], $this->manager->getSplitNodeDelimiters());
        $this->assertEquals(['*', '/',], $this->manager->getSplitNodeDelimiters(false));
        $this->assertEquals(['*', '/',], $this->manager->getSplitNodeDelimiters(true));
    }

    public function testIsRecursiveParentNode()
    {
        $this->assertFalse($this->manager->isRecursiveParentNode($this->docs[4]->firstChild));
        $this->assertFalse($this->manager->isRecursiveParentNode($this->docs[4]->childNodes->item(1)));
        $this->assertFalse($this->manager->isRecursiveParentNode($this->docs[5]->firstChild));
        $this->assertTrue($this->manager->isRecursiveParentNode($this->docs[16]->firstChild));
        $this->assertTrue($this->manager->isRecursiveParentNode($this->docs[17]->firstChild));
    }

    public function testIsRecursiveChildNode()
    {
        $this->assertFalse($this->manager->isRecursiveChildNode($this->docs[4]->firstChild->firstChild));
        $this->assertFalse($this->manager->isRecursiveChildNode($this->docs[4]->childNodes->item(1)->firstChild));
        $this->assertFalse($this->manager->isRecursiveChildNode($this->docs[4]->firstChild));
        $this->assertTrue($this->manager->isRecursiveChildNode($this->docs[16]->firstChild->firstChild));
        $this->assertTrue($this->manager->isRecursiveChildNode($this->docs[17]->firstChild->firstChild));
    }

    public function testIsPairParent()
    {
        $this->assertFalse($this->manager->isPairParentNode($this->docs[9]->firstChild));
        $this->assertFalse($this->manager->isPairParentNode($this->docs[4]->firstChild));
        $this->assertTrue($this->manager->isPairParentNode($this->docs[19]->firstChild));
    }

    public function testSetPairParentNode()
    {
        $this->assertInstanceOf(CheckNode::class, $this->manager->setPairParentNode($this->docs[19]->firstChild));
        $this->assertNull($this->manager->setPairParentNode($this->docs[9]->firstChild));
    }

    public function testClearPairParentNode()
    {
        $this->manager->setPairParentNode($this->docs[19]->firstChild);
        $this->assertTrue($this->manager->isPairSideANode($this->docs[19]->firstChild->firstChild));
        $this->manager->clearPairParentNode();
        $this->assertFalse($this->manager->isPairSideANode($this->docs[19]->firstChild->firstChild));
    }

    public function testIsSideANode()
    {
        $this->assertFalse($this->manager->isPairSideANode($this->docs[19]->firstChild->firstChild));
        $this->manager->setPairParentNode($this->docs[19]->firstChild);
        $this->assertTrue($this->manager->isPairSideANode($this->docs[19]->firstChild->firstChild));
        $this->assertFalse($this->manager->isPairSideANode($this->docs[19]->firstChild->childNodes->item(1)));
    }

    public function testIsSideBNode()
    {
        $this->assertFalse($this->manager->isPairSideBNode($this->docs[19]->firstChild->childNodes->item(1)));
        $this->manager->setPairParentNode($this->docs[19]->firstChild);
        $this->assertTrue($this->manager->isPairSideBNode($this->docs[19]->firstChild->childNodes->item(1)));
        $this->assertFalse($this->manager->isPairSideBNode($this->docs[19]->firstChild->childNodes->item(0)));
    }

    public function testIsRowColParentNode()
    {
        $this->assertTrue($this->manager->isRowColParentNode($this->docs[18]->firstChild));
        $this->assertFalse($this->manager->isRowColParentNode($this->docs[9]->firstChild));
    }

    public function testSetRowColParentNode()
    {
        $this->assertInstanceOf(CheckNode::class, $this->manager->setRowColParentNode($this->docs[18]->firstChild));
        $this->assertNull($this->manager->setRowColParentNode($this->docs[14]->firstChild));
    }

    public function testClearRowColParentNode()
    {
        $this->manager->setRowColParentNode($this->docs[18]->firstChild);
        $this->assertTrue($this->manager->rowColHasHeaders());
        $this->manager->clearRowColParentNode();
        $this->assertFalse($this->manager->rowColHasHeaders());
    }

    public function testRowColHasHeaders()
    {
        $this->assertTrue($this->manager->setRowColParentNode($this->docs[18]->firstChild)->rowColHasHeaders());
    }

    public function testRowColIsHeaderSectionNode()
    {
        $this->assertTrue(
            $this->manager->setRowColParentNode($this->docs[18]->firstChild)
                ->isRowColHeaderSectionNode($this->docs[18]->firstChild->childNodes->item(1))
        );
        $this->assertFalse(
            $this->manager->setRowColParentNode($this->docs[18]->firstChild)
                ->isRowColHeaderSectionNode($this->docs[18]->firstChild->childNodes->item(0))
        );
    }

    public function testIsRowColRowGroupNode()
    {
        $this->assertTrue(
            $this->manager->setRowColParentNode($this->docs[18]->firstChild)
                ->isRowColRowGroupNode($this->docs[18]->firstChild->childNodes->item(3))
        );
    }

    public function testIsRowColRowNode()
    {
        $this->assertTrue(
            $this->manager->setRowColParentNode($this->docs[18]->firstChild)
                ->isRowColRowNode($this->docs[18]->firstChild->childNodes->item(1)->childNodes->item(1))
        );
        $this->assertFalse(
            $this->manager->setRowColParentNode($this->docs[18]->firstChild)
                ->isRowColRowNode($this->docs[18]->firstChild->childNodes->item(3)->childNodes->item(1))
        );
    }
}
