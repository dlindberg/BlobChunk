<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Manager;

use dlindberg\BlobChunk\Manager\Config as Config;
use dlindberg\BlobChunk\Manager\Recursive as Recursive;
use dlindberg\DOMDocumentFactory\DOMDocumentFactory;
use dlindberg\DOMDocumentFactory\DOMDocumentFactoryConfig;

final class RecursiveTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $testA = [
        ['group' => 'recursive', 'parents' => ['tags' => ['ul', 'ol',]], 'children' => ['tags' => ['li',],],],
    ];

    /**
     * @var array
     */
    private $testB = [
        [
            'group'    => 'recursive',
            'parents'  => [
                'attributes' => [
                    ['name' => 'id', 'value' => 'a'],
                    ['name' => 'class', 'value' => 'b'],
                ],
            ],
            'children' => [
                'attributes' => [
                    ['name' => 'id', 'value' => 'c'],
                    ['name' => 'class', 'value' => 'd'],
                ],
            ],
        ],
    ];

    /**
     * @var array
     */
    private $testC = [
        [
            'group'    => 'recursive',
            'parents'  => [
                'tags'       => ['ul', 'ol'],
                'attributes' => [
                    ['name' => 'id', 'value' => 'a'],
                    ['name' => 'class', 'value' => 'b'],
                ],
            ],
            'children' => [
                'tags'       => ['li',],
                'attributes' => [
                    ['name' => 'id', 'value' => 'c'],
                    ['name' => 'class', 'value' => 'd'],
                ],
            ],
        ],
    ];
    /**
     * @var Recursive
     */
    private $rA;
    /**
     * @var Recursive
     */
    private $rB;
    /**
     * @var Recursive
     */
    private $rC;
    /**
     * @var DOMDocumentFactory
     */
    private $docFactory;

    /**
     * @var string[]
     */
    private $samples = [
        '<ul><li>sample list</li></ul><ol><li>sample ordered item</li></ol>',
        '<ul id="a"><li id="c">sample list</li></ul><ol class="b"><li class="d">sample ordered item</li></ol>',
        '<div id="a"><div id="c">sample list</div></div><div class="b"><div class="d">sample ordered item</div></div>',
    ];
    /**
     * @var \DOMElement[]
     */
    private $samplesDocs = [];

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->rA = new Recursive(new Config($this->testA));
        $this->rB = new Recursive(new Config($this->testB));
        $this->rC = new Recursive(new Config($this->testC));
        $pureConfig = \HTMLPurifier_Config::createDefault();
        $pureConfig->set('Attr.EnableID', true);
        $this->docFactory = new DOMDocumentFactory(
            (new DOMDocumentFactoryConfig())->setInputPurifier(new \HTMLPurifier($pureConfig))
        );
        $this->samplesDocs = \array_map(function ($sample) {
            return $this->docFactory->getNode($sample);
        }, $this->samples);
    }

    public function testIsParentA()
    {
        $this->assertTrue($this->rA->isParent($this->samplesDocs[0]->childNodes->item(0)));
        $this->assertTrue($this->rA->isParent($this->samplesDocs[0]->childNodes->item(1)));
        $this->assertTrue($this->rA->isParent($this->samplesDocs[1]->childNodes->item(0)));
        $this->assertTrue($this->rA->isParent($this->samplesDocs[1]->childNodes->item(1)));
        $this->assertFalse($this->rA->isParent($this->samplesDocs[2]->childNodes->item(0)));
        $this->assertFalse($this->rA->isParent($this->samplesDocs[2]->childNodes->item(1)));
    }

    public function testIsParentB()
    {
        $this->assertFalse($this->rB->isParent($this->samplesDocs[0]->childNodes->item(0)));
        $this->assertFalse($this->rB->isParent($this->samplesDocs[0]->childNodes->item(1)));
        $this->assertTrue($this->rB->isParent($this->samplesDocs[1]->childNodes->item(0)));
        $this->assertTrue($this->rB->isParent($this->samplesDocs[1]->childNodes->item(1)));
        $this->assertTrue($this->rB->isParent($this->samplesDocs[2]->childNodes->item(0)));
        $this->assertTrue($this->rB->isParent($this->samplesDocs[2]->childNodes->item(1)));
    }

    public function testIsParentC()
    {
        $this->assertTrue($this->rC->isParent($this->samplesDocs[0]->childNodes->item(0)));
        $this->assertTrue($this->rC->isParent($this->samplesDocs[0]->childNodes->item(1)));
        $this->assertTrue($this->rC->isParent($this->samplesDocs[1]->childNodes->item(0)));
        $this->assertTrue($this->rC->isParent($this->samplesDocs[1]->childNodes->item(1)));
        $this->assertTrue($this->rC->isParent($this->samplesDocs[2]->childNodes->item(0)));
        $this->assertTrue($this->rC->isParent($this->samplesDocs[2]->childNodes->item(1)));
    }

    public function testIsChildA()
    {
        $this->assertTrue($this->rA->isChild($this->samplesDocs[0]->childNodes->item(0)->firstChild));
        $this->assertTrue($this->rA->isChild($this->samplesDocs[0]->childNodes->item(1)->firstChild));
        $this->assertTrue($this->rA->isChild($this->samplesDocs[1]->childNodes->item(0)->firstChild));
        $this->assertTrue($this->rA->isChild($this->samplesDocs[1]->childNodes->item(1)->firstChild));
        $this->assertFalse($this->rA->isChild($this->samplesDocs[2]->childNodes->item(0)->firstChild));
        $this->assertFalse($this->rA->isChild($this->samplesDocs[2]->childNodes->item(1)->firstChild));
    }

    public function testIsChildB()
    {
        $this->assertFalse($this->rB->isChild($this->samplesDocs[0]->childNodes->item(0)->firstChild));
        $this->assertFalse($this->rB->isChild($this->samplesDocs[0]->childNodes->item(1)->firstChild));
        $this->assertTrue($this->rB->isChild($this->samplesDocs[1]->childNodes->item(0)->firstChild));
        $this->assertTrue($this->rB->isChild($this->samplesDocs[1]->childNodes->item(1)->firstChild));
        $this->assertTrue($this->rB->isChild($this->samplesDocs[2]->childNodes->item(0)->firstChild));
        $this->assertTrue($this->rB->isChild($this->samplesDocs[2]->childNodes->item(1)->firstChild));
    }

    public function testIsChildC()
    {
        $this->assertTrue($this->rC->isChild($this->samplesDocs[0]->childNodes->item(0)->firstChild));
        $this->assertTrue($this->rC->isChild($this->samplesDocs[0]->childNodes->item(1)->firstChild));
        $this->assertTrue($this->rC->isChild($this->samplesDocs[1]->childNodes->item(0)->firstChild));
        $this->assertTrue($this->rC->isChild($this->samplesDocs[1]->childNodes->item(1)->firstChild));
        $this->assertTrue($this->rC->isChild($this->samplesDocs[2]->childNodes->item(0)->firstChild));
        $this->assertTrue($this->rC->isChild($this->samplesDocs[2]->childNodes->item(1)->firstChild));
    }

    public function testGetParents()
    {
        $this->assertEquals($this->testA[0]['parents']['tags'], $this->rA->getParents()['tags']);
        $this->assertEmpty($this->rA->getParents()['attributes']);
        $this->assertEmpty($this->rB->getParents()['tags']);
        $this->assertEquals($this->testB[0]['parents']['attributes'], $this->rB->getParents()['attributes']);
        $this->assertEquals($this->testC[0]['parents']['tags'], $this->rC->getParents()['tags']);
        $this->assertEquals($this->testC[0]['parents']['attributes'], $this->rC->getParents()['attributes']);
    }

    public function testGetChildren()
    {
        $this->assertEquals($this->testA[0]['children']['tags'], $this->rA->getChildren()['tags']);
        $this->assertEmpty($this->rA->getChildren()['attributes']);
        $this->assertEmpty($this->rB->getChildren()['tags']);
        $this->assertEquals($this->testB[0]['children']['attributes'], $this->rB->getChildren()['attributes']);
        $this->assertEquals($this->testC[0]['children']['tags'], $this->rC->getChildren()['tags']);
        $this->assertEquals($this->testC[0]['children']['attributes'], $this->rC->getChildren()['attributes']);
    }
}
