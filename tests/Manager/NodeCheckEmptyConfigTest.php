<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Manager;

use dlindberg\DOMDocumentFactory\DOMDocumentFactory;
use dlindberg\DOMDocumentFactory\DOMDocumentFactoryConfig;

final class NodeCheckEmptyConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $testData = [];
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
        $this->assertEquals('default', $this->manager->getType($this->docs[0]->firstChild));
        $this->assertEquals('default', $this->manager->getType($this->docs[1]->firstChild));
        $this->assertEquals('default', $this->manager->getType($this->docs[3]->firstChild));
        $this->assertEquals('default', $this->manager->getType($this->docs[4]->firstChild));
        $this->assertEquals('default', $this->manager->getType($this->docs[8]->firstChild));
        $this->assertEquals('default', $this->manager->getType($this->docs[9]->firstChild));
        $this->assertEquals('default', $this->manager->getType($this->docs[8]->firstChild->firstChild));
    }

    public function testIsParentNode()
    {
        $this->assertFalse($this->manager->isParentNode($this->docs[0]->firstChild));
        $this->assertFalse($this->manager->isParentNode($this->docs[1]->firstChild));
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
    }

    public function testIsSplitNode()
    {
        $this->assertFalse($this->manager->isSplitNode($this->docs[3]->firstChild));
        $this->assertFalse($this->manager->isSplitNode($this->docs[1]->firstChild));
    }

    public function testGetSplitNodeDelimiters()
    {
        $this->assertEmpty($this->manager->getSplitNodeDelimiters());
        $this->assertEmpty($this->manager->getSplitNodeDelimiters(false));
        $this->assertEmpty($this->manager->getSplitNodeDelimiters(true));
    }

    public function testIsRecursiveParentNode()
    {
        $this->assertFalse($this->manager->isRecursiveParentNode($this->docs[4]->firstChild));
        $this->assertFalse($this->manager->isRecursiveParentNode($this->docs[4]->childNodes->item(1)));
        $this->assertFalse($this->manager->isRecursiveParentNode($this->docs[5]->firstChild));
    }

    public function testIsRecursiveChildNode()
    {
        $this->assertFalse($this->manager->isRecursiveChildNode($this->docs[4]->firstChild->firstChild));
        $this->assertFalse($this->manager->isRecursiveChildNode($this->docs[4]->childNodes->item(1)->firstChild));
        $this->assertFalse($this->manager->isRecursiveChildNode($this->docs[4]->firstChild));
    }

    public function testIsPairParent()
    {
        $this->assertFalse($this->manager->isPairParentNode($this->docs[9]->firstChild));
        $this->assertFalse($this->manager->isPairParentNode($this->docs[4]->firstChild));
    }

    public function testSetPairParentNode()
    {
        $this->assertNull($this->manager->setPairParentNode($this->docs[9]->firstChild));
        $this->assertNull($this->manager->setPairParentNode($this->docs[8]->firstChild));
    }

    public function testClearPairParentNode()
    {
        $this->manager->setPairParentNode($this->docs[9]->firstChild);
        $this->assertFalse($this->manager->isPairSideANode($this->docs[9]->firstChild->firstChild));
        $this->manager->clearPairParentNode();
        $this->assertFalse($this->manager->isPairSideANode($this->docs[9]->firstChild->firstChild));
    }

    public function testIsSideANode()
    {
        $this->assertFalse($this->manager->isPairSideANode($this->docs[9]->firstChild->firstChild));
        $this->manager->setPairParentNode($this->docs[9]->firstChild);
        $this->assertFalse($this->manager->isPairSideANode($this->docs[9]->firstChild->firstChild));
        $this->assertFalse($this->manager->isPairSideANode($this->docs[9]->firstChild->childNodes->item(1)));
    }

    public function testIsSideBNode()
    {
        $this->assertFalse($this->manager->isPairSideBNode($this->docs[9]->firstChild->childNodes->item(1)));
        $this->manager->setPairParentNode($this->docs[9]->firstChild);
        $this->assertFalse($this->manager->isPairSideBNode($this->docs[9]->firstChild->childNodes->item(1)));
        $this->assertFalse($this->manager->isPairSideBNode($this->docs[9]->firstChild->childNodes->item(0)));
    }

    public function testIsRowColParentNode()
    {
        $this->assertFalse($this->manager->isRowColParentNode($this->docs[8]->firstChild));
        $this->assertFalse($this->manager->isRowColParentNode($this->docs[14]->firstChild));
        $this->assertFalse($this->manager->isRowColParentNode($this->docs[9]->firstChild));
    }

    public function testSetRowColParentNode()
    {
        $this->assertNull($this->manager->setRowColParentNode($this->docs[8]->firstChild));
        $this->assertNull($this->manager->setRowColParentNode($this->docs[9]->firstChild));
        $this->assertNull($this->manager->setRowColParentNode($this->docs[14]->firstChild));
        $this->assertNull($this->manager->setRowColParentNode($this->docs[7]->firstChild));
    }

    public function testClearRowColParentNode()
    {
        $this->manager->setRowColParentNode($this->docs[8]->firstChild);
        $this->assertFalse($this->manager->rowColHasHeaders());
        $this->manager->clearRowColParentNode();
        $this->assertFalse($this->manager->rowColHasHeaders());
    }
}
