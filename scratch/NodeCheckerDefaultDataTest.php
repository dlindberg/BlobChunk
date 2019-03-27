<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Manager;

use dlindberg\BlobChunk\Manager\Config as Config;
use dlindberg\DOMDocumentFactory\DOMDocumentFactory;

final class NodeCheckerDefaultDataTest extends \PHPUnit\Framework\TestCase
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
    ];
    /**
     * @var \DOMElement[]
     */
    private $samplesDocs = [];

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->config = new Config($this->testData);
        $this->docFactory = new DOMDocumentFactory();
        $this->samplesDocs = \array_map(function ($sample) {
            return $this->docFactory->getNode($sample);
        }, $this->samples);
    }

    public function testNameGroup()
    {
        $this->assertEquals(
            'parent',
            (new NodeCheckChecker($this->config))->nameGroup($this->samplesDocs[0]->firstChild)
        );
        $this->assertEquals(
            'special',
            (new NodeCheckChecker($this->config))->nameGroup($this->samplesDocs[1]->firstChild)
        );
        $this->assertEquals(
            'split',
            (new NodeCheckChecker($this->config))->nameGroup($this->samplesDocs[2]->firstChild)
        );
        $this->assertEquals(
            'nest',
            (new NodeCheckChecker($this->config))->nameGroup($this->samplesDocs[3]->firstChild)
        );
        $this->assertEquals(
            'default',
            (new NodeCheckChecker($this->config))->nameGroup($this->samplesDocs[3]->firstChild->firstChild)
        );
    }

    public function testIsParent()
    {
        $this->assertTrue((new NodeCheckChecker($this->config))
            ->isParent($this->samplesDocs[0]->firstChild));
        $this->assertFalse((new NodeCheckChecker($this->config))
            ->isParent($this->samplesDocs[0]->childNodes->item(1)));
    }

    public function testIsSpecial()
    {
        $this->assertTrue((new NodeCheckChecker($this->config))
            ->isSpecial($this->samplesDocs[1]->childNodes->item(0)));
        $this->assertTrue((new NodeCheckChecker($this->config))
            ->isSpecial($this->samplesDocs[1]->childNodes->item(1)));
        $this->assertTrue((new NodeCheckChecker($this->config))
            ->isSpecial($this->samplesDocs[1]->childNodes->item(2)));
        $this->assertTrue((new NodeCheckChecker($this->config))
            ->isSpecial($this->samplesDocs[1]->childNodes->item(3)));
        $this->assertTrue((new NodeCheckChecker($this->config))
            ->isSpecial($this->samplesDocs[1]->childNodes->item(4)));
        $this->assertTrue((new NodeCheckChecker($this->config))
            ->isSpecial($this->samplesDocs[1]->childNodes->item(5)));
        $this->assertTrue((new NodeCheckChecker($this->config))
            ->isSpecial($this->samplesDocs[4]->firstChild->childNodes->item(0)));
        $this->assertTrue((new NodeCheckChecker($this->config))
            ->isSpecial($this->samplesDocs[4]->firstChild->childNodes->item(2)));
        $this->assertTrue((new NodeCheckChecker($this->config))
            ->isSpecial($this->samplesDocs[4]->firstChild->childNodes->item(4)));
        $this->assertTrue((new NodeCheckChecker($this->config))
            ->isSpecial($this->samplesDocs[4]->firstChild->childNodes->item(6)));
        $this->assertTrue((new NodeCheckChecker($this->config))
            ->isSpecial($this->samplesDocs[5]->firstChild->firstChild));
        $this->assertFalse((new NodeCheckChecker($this->config))
            ->isSpecial($this->samplesDocs[5]->firstChild));
        $this->assertFalse((new NodeCheckChecker($this->config))
            ->isSpecial($this->samplesDocs[4]->firstChild));
    }

    public function testIsSplit()
    {
        $this->assertTrue((new NodeCheckChecker($this->config))
            ->isSplit($this->samplesDocs[0]->childNodes->item(1)));
        $this->assertTrue((new NodeCheckChecker($this->config))
            ->isSplit($this->samplesDocs[2]->childNodes->item(0)));
        $this->assertTrue((new NodeCheckChecker($this->config))
            ->isSplit($this->samplesDocs[6]->firstChild->firstChild));
        $this->assertFalse((new NodeCheckChecker($this->config))
            ->isSplit($this->samplesDocs[6]->firstChild));
        $this->assertFalse((new NodeCheckChecker($this->config))
            ->isSplit($this->samplesDocs[5]->firstChild->firstChild));
    }

    public function testGetSplitDelimiters()
    {
        $this->assertEquals($this->testData[5]['on'], (new NodeCheckChecker($this->config))->getSplitDelimiters(false));
        $this->assertEquals(['.', '?', '!'], (new NodeCheckChecker($this->config))->getSplitDelimiters(true));
        $this->assertNotEquals($this->testData[5]['on'], (new NodeCheckChecker($this->config))->getSplitDelimiters(true));
    }
}
