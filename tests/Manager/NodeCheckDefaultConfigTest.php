<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Manager;

use dlindberg\DOMDocumentFactory\DOMDocumentFactory;
use dlindberg\DOMDocumentFactory\DOMDocumentFactoryConfig;

final class NodeCheckDefaultConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $testData = [
        ['group' => 'parents', 'tags' => ['div'],],
        ['group' => 'specials', 'tags' => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'strong', 'em', 'b', 'i', 'a'],],
        [
            'group' => 'rowCol',
            [
                'parent'    => ['type' => 'tag', 'value' => 'table',],
                'colDefs'   => ['type' => 'tag', 'value' => 'thead',],
                'rowsGroup' => ['type' => 'tag', 'value' => 'tbody',],
                'row'       => ['type' => 'tag', 'value' => 'tr',],
            ],
        ],
        ['group' => 'recursive', 'parents' => ['tags' => ['ul', 'ol',]], 'children' => ['tags' => ['li',],],],
        [
            'group' => 'pairs',
            [
                'parent' => ['type' => 'tag', 'value' => 'dl',],
                'a'      => ['type' => 'tag', 'value' => 'dt',],
                'b'      => ['type' => 'tag', 'value' => 'dd',],
            ],
        ],
        ['group' => 'splits', 'what' => ['tags' => ['p'],], 'on' => ['. ', '? ', '! ']],
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
        10 => '<div id="a">Test side one</div><div class="b">test side tow</div>',
        11 => '<div id="c">Test side one</div><div class="d">test side two</div>',
        12 => '<div id="i">Test side one</div><div class="j">test side two</div><div class="e">test e table</div>',
        13 => '<div id="f">Test side one</div><div class="g">test side two</div><div class="h">test side three</div>',
        14 => '<table>
                   <thead>
                       <tr><th>test 0 0 0 </th><th>test 0 0 1</th></tr>
                   </thead>
                   <tbody>
                       <tr><td>test 0 1 0</td><td>test 0 1 1</td></tr>
                   </tbody>
               </table>',
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
        $this->assertEquals('parent', $this->manager->getType($this->docs[0]->firstChild));
        $this->assertEquals('special', $this->manager->getType($this->docs[1]->firstChild));
        $this->assertEquals('split', $this->manager->getType($this->docs[3]->firstChild));
        $this->assertEquals('recursive', $this->manager->getType($this->docs[4]->firstChild));
        $this->assertEquals('rowCol', $this->manager->getType($this->docs[8]->firstChild));
        $this->assertEquals('pairs', $this->manager->getType($this->docs[9]->firstChild));
        $this->assertEquals('default', $this->manager->getType($this->docs[8]->firstChild->firstChild));
    }

    public function testIsParentNode()
    {
        $this->assertTrue($this->manager->isParentNode($this->docs[0]->firstChild));
        $this->assertFalse($this->manager->isParentNode($this->docs[1]->firstChild));
    }

    public function testIsSpecialNode()
    {
        $this->assertTrue($this->manager->isSpecialNode($this->docs[1]->childNodes->item(0)));
        $this->assertTrue($this->manager->isSpecialNode($this->docs[1]->childNodes->item(1)));
        $this->assertTrue($this->manager->isSpecialNode($this->docs[1]->childNodes->item(2)));
        $this->assertTrue($this->manager->isSpecialNode($this->docs[2]->childNodes->item(0)));
        $this->assertTrue($this->manager->isSpecialNode($this->docs[2]->childNodes->item(1)));
        $this->assertTrue($this->manager->isSpecialNode($this->docs[2]->childNodes->item(2)));
        $this->assertTrue($this->manager->isSpecialNode($this->docs[5]->firstChild->firstChild));
        $this->assertTrue($this->manager->isSpecialNode($this->docs[5]->firstChild->childNodes->item(2)));
        $this->assertTrue($this->manager->isSpecialNode($this->docs[5]->firstChild->childNodes->item(4)));
        $this->assertTrue($this->manager->isSpecialNode($this->docs[5]->firstChild->childNodes->item(6)));
        $this->assertTrue($this->manager->isSpecialNode($this->docs[6]->firstChild->firstChild));
        $this->assertFalse($this->manager->isSpecialNode($this->docs[0]->firstChild));
    }

    public function testIsSplitNode()
    {
        $this->assertTrue($this->manager->isSplitNode($this->docs[3]->firstChild));
        $this->assertFalse($this->manager->isSplitNode($this->docs[1]->firstChild));
    }

    public function testGetSplitNodeDelimiters()
    {
        $this->assertEquals(['. ', '? ', '! '], $this->manager->getSplitNodeDelimiters());
        $this->assertEquals(['. ', '? ', '! '], $this->manager->getSplitNodeDelimiters(false));
        $this->assertEquals(['.', '?', '!'], $this->manager->getSplitNodeDelimiters(true));
    }

    public function testIsRecursiveParentNode()
    {
        $this->assertTrue($this->manager->isRecursiveParentNode($this->docs[4]->firstChild));
        $this->assertTrue($this->manager->isRecursiveParentNode($this->docs[4]->childNodes->item(1)));
        $this->assertFalse($this->manager->isRecursiveParentNode($this->docs[5]->firstChild));
    }

    public function testIsRecursiveChildNode()
    {
        $this->assertTrue($this->manager->isRecursiveChildNode($this->docs[4]->firstChild->firstChild));
        $this->assertTrue($this->manager->isRecursiveChildNode($this->docs[4]->childNodes->item(1)->firstChild));
        $this->assertFalse($this->manager->isRecursiveChildNode($this->docs[4]->firstChild));
    }

    public function testIsPairParent()
    {
        $this->assertTrue($this->manager->isPairParentNode($this->docs[9]->firstChild));
        $this->assertFalse($this->manager->isPairParentNode($this->docs[4]->firstChild));
    }

    public function testSetPairParentNode()
    {
        $this->assertInstanceOf(CheckNode::class, $this->manager->setPairParentNode($this->docs[9]->firstChild));
        $this->assertNull($this->manager->setPairParentNode($this->docs[8]->firstChild));
    }

    public function testClearPairParentNode()
    {
        $this->manager->setPairParentNode($this->docs[9]->firstChild);
        $this->assertTrue($this->manager->isPairSideANode($this->docs[9]->firstChild->firstChild));
        $this->manager->clearPairParentNode();
        $this->assertFalse($this->manager->isPairSideANode($this->docs[9]->firstChild->firstChild));
    }

    public function testIsSideANode()
    {
        $this->assertFalse($this->manager->isPairSideANode($this->docs[9]->firstChild->firstChild));
        $this->manager->setPairParentNode($this->docs[9]->firstChild);
        $this->assertTrue($this->manager->isPairSideANode($this->docs[9]->firstChild->firstChild));
        $this->assertFalse($this->manager->isPairSideANode($this->docs[9]->firstChild->childNodes->item(1)));
    }

    public function testIsSideBNode()
    {
        $this->assertFalse($this->manager->isPairSideBNode($this->docs[9]->firstChild->childNodes->item(1)));
        $this->manager->setPairParentNode($this->docs[9]->firstChild);
        $this->assertTrue($this->manager->isPairSideBNode($this->docs[9]->firstChild->childNodes->item(1)));
        $this->assertFalse($this->manager->isPairSideBNode($this->docs[9]->firstChild->childNodes->item(0)));
    }

    public function testIsRowColParentNode()
    {
        $this->assertTrue($this->manager->isRowColParentNode($this->docs[8]->firstChild));
        $this->assertTrue($this->manager->isRowColParentNode($this->docs[14]->firstChild));
        $this->assertFalse($this->manager->isRowColParentNode($this->docs[9]->firstChild));
    }

    public function testSetRowColParentNode()
    {
        $this->assertInstanceOf(CheckNode::class, $this->manager->setRowColParentNode($this->docs[8]->firstChild));
        $this->assertNull($this->manager->setRowColParentNode($this->docs[9]->firstChild));
        $this->assertInstanceOf(CheckNode::class, $this->manager->setRowColParentNode($this->docs[14]->firstChild));
        $this->assertNull($this->manager->setRowColParentNode($this->docs[7]->firstChild));
    }

    public function testClearRowColParentNode()
    {
        $this->manager->setRowColParentNode($this->docs[8]->firstChild);
        $this->assertTrue($this->manager->rowColHasHeaders());
        $this->manager->clearRowColParentNode();
        $this->assertFalse($this->manager->rowColHasHeaders());
    }

    public function testRowColHasHeaders()
    {
        $this->assertTrue($this->manager->setRowColParentNode($this->docs[8]->firstChild)->rowColHasHeaders());
    }

    public function testRowColIsHeaderSectionNode()
    {
        $this->assertTrue(
            $this->manager->setRowColParentNode($this->docs[14]->firstChild)
                ->isRowColHeaderSectionNode($this->docs[14]->firstChild->childNodes->item(1))
        );
        $this->assertFalse(
            $this->manager->setRowColParentNode($this->docs[14]->firstChild)
                ->isRowColHeaderSectionNode($this->docs[14]->firstChild->childNodes->item(3))
        );
        $this->assertFalse(
            $this->manager->setRowColParentNode($this->docs[8]->firstChild)
                ->isRowColHeaderSectionNode($this->docs[8]->firstChild->childNodes->item(0))
        );
    }

    public function testIsRowColRowGroupNode()
    {
        $this->assertFalse(
            $this->manager->setRowColParentNode($this->docs[14]->firstChild)
                ->isRowColRowGroupNode($this->docs[14]->firstChild->childNodes->item(1))
        );
        $this->assertTrue(
            $this->manager->setRowColParentNode($this->docs[14]->firstChild)
                ->isRowColRowGroupNode($this->docs[14]->firstChild->childNodes->item(3))
        );
        $this->assertFalse(
            $this->manager->setRowColParentNode($this->docs[8]->firstChild)
                ->isRowColRowGroupNode($this->docs[8]->firstChild->childNodes->item(0))
        );
    }

    public function testIsRowColRowNode()
    {
        $this->assertTrue(
            $this->manager->setRowColParentNode($this->docs[14]->firstChild)
                ->isRowColRowNode($this->docs[14]->firstChild->childNodes->item(1)->childNodes->item(1))
        );
        $this->assertTrue(
            $this->manager->setRowColParentNode($this->docs[14]->firstChild)
                ->isRowColRowNode($this->docs[14]->firstChild->childNodes->item(3)->childNodes->item(1))
        );
        $this->assertTrue(
            $this->manager->setRowColParentNode($this->docs[8]->firstChild)
                ->isRowColRowNode($this->docs[8]->firstChild->childNodes->item(1))
        );
        $this->assertTrue(
            $this->manager->setRowColParentNode($this->docs[8]->firstChild)
                ->isRowColRowNode($this->docs[8]->firstChild->childNodes->item(1))
        );
        $this->assertFalse(
            $this->manager->setRowColParentNode($this->docs[8]->firstChild)
                ->isRowColRowNode($this->docs[8]->firstChild->childNodes->item(0))
        );
    }
}
