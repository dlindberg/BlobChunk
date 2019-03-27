<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Manager;

use dlindberg\BlobChunk\Manager\Config as Config;
use dlindberg\BlobChunk\Manager\Pairs as Pairs;
use dlindberg\DOMDocumentFactory\DOMDocumentFactory;
use dlindberg\DOMDocumentFactory\DOMDocumentFactoryConfig;

final class PairsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $tests = [
        0 => [
            [
                'group' => 'pairs',
                [
                    'parent' => ['type' => 'tag', 'value' => 'dl',],
                    'a'      => ['type' => 'tag', 'value' => 'dt',],
                    'b'      => ['type' => 'tag', 'value' => 'dd',],
                ],
            ],
        ],
        1 => [
            [
                'group' => 'pairs',
                [
                    'parent' => ['type' => 'attribute', 'name' => 'class', 'value' => 'a',],
                    'a'      => ['type' => 'attribute', 'name' => 'class', 'value' => 'b',],
                    'b'      => ['type' => 'attribute', 'name' => 'class', 'value' => 'c',],
                ],
            ],
        ],
        2 => [
            [
                'group' => 'pairs',
                [
                    'parent' => ['type' => 'tag', 'value' => 'dl',],
                    'a'      => ['type' => 'tag', 'value' => 'dt',],
                    'b'      => ['type' => 'tag', 'value' => 'dd',],
                ],
                [
                    'parent' => ['type' => 'attribute', 'name' => 'class', 'value' => 'a',],
                    'a'      => ['type' => 'attribute', 'name' => 'class', 'value' => 'b',],
                    'b'      => ['type' => 'attribute', 'name' => 'class', 'value' => 'c',],
                ],
            ],
        ],
        3 => [
            [
                'group' => 'pairs',
                [
                    'parent' => ['type' => 'attribute', 'name' => 'class', 'value' => 'a',],
                    'a'      => ['type' => 'attribute', 'name' => 'class', 'value' => 'b',],
                    'b'      => ['type' => 'attribute', 'name' => 'class', 'value' => 'c',],
                ],
                [
                    'parent' => ['type' => 'tag', 'value' => 'dl',],
                    'a'      => ['type' => 'tag', 'value' => 'dt',],
                    'b'      => ['type' => 'tag', 'value' => 'dd',],
                ],
            ],
        ],
    ];

    private $samples = [
        '<dl><dt>test 0 a</dt><dd>test 0 b</dd></dl>',
        '<dl class="a"><dt class="b">test 1 a</dt><dd class="c">test 1 b</dd></dl>',
        '<div class="a"><div class="b">test 2 a</div><div class="c">test 2 b</div></div>',
        '<dl class="a"><dt class="d">test 3 a</dt><dd class="e">test 3 b</dd></dl>',
        '<div class="a"><div class="d">test 4 a</div><div class="e">test 4 b</div></div>',
    ];
    /**
     * @var \DOMElement[]
     */
    private $docs = [];

    /**
     * @var Pairs[]
     */
    private $pairs;
    /**
     * @var DOMDocumentFactory
     */
    private $docFactory;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        \array_walk($this->tests, function ($test) {
            $this->pairs[] = new Pairs(new Config($test));
        });
        $pureConfig = \HTMLPurifier_Config::createDefault();
        $pureConfig->set('Attr.EnableID', true);
        $this->docFactory = new DOMDocumentFactory(
            (new DOMDocumentFactoryConfig())->setInputPurifier(new \HTMLPurifier($pureConfig))
        );
        $this->docs = \array_map(function ($sample) {
            return $this->docFactory->getNode($sample)->firstChild;
        }, $this->samples);
    }

    public function testSample0()
    {
        $set0 = $this->pairs[0]->getSet($this->docs[0]);
        $set2 = $this->pairs[2]->getSet($this->docs[0]);
        $set3 = $this->pairs[3]->getSet($this->docs[0]);
        $this->assertInstanceOf(Pairs::class, $set0);
        $this->assertEquals($this->tests[0][0][0]['a'], $set0->sideA);
        $this->assertEquals($this->tests[0][0][0]['b'], $set0->sideB);
        $this->assertInstanceOf(Pairs::class, $set2);
        $this->assertEquals($this->tests[2][0][0]['a'], $set2->sideA);
        $this->assertEquals($this->tests[2][0][0]['b'], $set2->sideB);
        $this->assertInstanceOf(Pairs::class, $set3);
        $this->assertEquals($this->tests[3][0][1]['a'], $set3->sideA);
        $this->assertEquals($this->tests[3][0][1]['b'], $set3->sideB);
    }

    public function testSample1()
    {
        $set0 = $this->pairs[0]->getSet($this->docs[1]);
        $set1 = $this->pairs[1]->getSet($this->docs[1]);
        $set2 = $this->pairs[2]->getSet($this->docs[1]);
        $set3 = $this->pairs[3]->getSet($this->docs[1]);
        $this->assertInstanceOf(Pairs::class, $set0);
        $this->assertEquals($this->tests[0][0][0]['a'], $set0->sideA);
        $this->assertEquals($this->tests[0][0][0]['b'], $set0->sideB);
        $this->assertInstanceOf(Pairs::class, $set1);
        $this->assertEquals($this->tests[1][0][0]['a'], $set1->sideA);
        $this->assertEquals($this->tests[1][0][0]['b'], $set1->sideB);
        $this->assertInstanceOf(Pairs::class, $set2);
        $this->assertEquals($this->tests[2][0][1]['a'], $set2->sideA);
        $this->assertEquals($this->tests[2][0][1]['b'], $set2->sideB);
        $this->assertInstanceOf(Pairs::class, $set3);
        $this->assertEquals($this->tests[3][0][1]['a'], $set3->sideA);
        $this->assertEquals($this->tests[3][0][1]['b'], $set3->sideB);
    }

    public function testSample2()
    {
        $set1 = $this->pairs[1]->getSet($this->docs[2]);
        $set2 = $this->pairs[2]->getSet($this->docs[2]);
        $set3 = $this->pairs[3]->getSet($this->docs[2]);
        $this->assertInstanceOf(Pairs::class, $set1);
        $this->assertEquals($this->tests[1][0][0]['a'], $set1->sideA);
        $this->assertEquals($this->tests[1][0][0]['b'], $set1->sideB);
        $this->assertInstanceOf(Pairs::class, $set2);
        $this->assertEquals($this->tests[2][0][1]['a'], $set2->sideA);
        $this->assertEquals($this->tests[2][0][1]['b'], $set2->sideB);
        $this->assertInstanceOf(Pairs::class, $set3);
        $this->assertEquals($this->tests[3][0][0]['a'], $set3->sideA);
        $this->assertEquals($this->tests[3][0][0]['b'], $set3->sideB);
    }

    public function testSample3()
    {
        $set0 = $this->pairs[0]->getSet($this->docs[3]);
        $set1 = $this->pairs[1]->getSet($this->docs[3]);
        $set2 = $this->pairs[2]->getSet($this->docs[3]);
        $set3 = $this->pairs[3]->getSet($this->docs[3]);
        $this->assertInstanceOf(Pairs::class, $set0);
        $this->assertEquals($this->tests[0][0][0]['a'], $set0->sideA);
        $this->assertEquals($this->tests[0][0][0]['b'], $set0->sideB);
        $this->assertInstanceOf(Pairs::class, $set1);
        $this->assertEquals($this->tests[1][0][0]['a'], $set1->sideA);
        $this->assertEquals($this->tests[1][0][0]['b'], $set1->sideB);
        $this->assertInstanceOf(Pairs::class, $set2);
        $this->assertEquals($this->tests[2][0][1]['a'], $set2->sideA);
        $this->assertEquals($this->tests[2][0][1]['b'], $set2->sideB);
        $this->assertInstanceOf(Pairs::class, $set3);
        $this->assertEquals($this->tests[3][0][1]['a'], $set3->sideA);
        $this->assertEquals($this->tests[3][0][1]['b'], $set3->sideB);
    }

    public function testSample4()
    {
        $set1 = $this->pairs[1]->getSet($this->docs[4]);
        $set2 = $this->pairs[2]->getSet($this->docs[4]);
        $set3 = $this->pairs[3]->getSet($this->docs[4]);
        $this->assertInstanceOf(Pairs::class, $set1);
        $this->assertEquals($this->tests[1][0][0]['a'], $set1->sideA);
        $this->assertEquals($this->tests[1][0][0]['b'], $set1->sideB);
        $this->assertInstanceOf(Pairs::class, $set2);
        $this->assertEquals($this->tests[2][0][1]['a'], $set2->sideA);
        $this->assertEquals($this->tests[2][0][1]['b'], $set2->sideB);
        $this->assertInstanceOf(Pairs::class, $set3);
        $this->assertEquals($this->tests[3][0][0]['a'], $set3->sideA);
        $this->assertEquals($this->tests[3][0][0]['b'], $set3->sideB);
    }

    public function testFailureSample02()
    {
        $this->expectExceptionMessage('array_merge() expects at least 1 parameter, 0 given');
        $this->pairs[0]->getSet($this->docs[2]);
    }

    public function testFailureSample04()
    {
        $this->expectExceptionMessage('array_merge() expects at least 1 parameter, 0 given');
        $this->pairs[0]->getSet($this->docs[4]);
    }

    public function testFailureSample10()
    {
        $this->expectExceptionMessage('array_merge() expects at least 1 parameter, 0 given');
        $this->pairs[1]->getSet($this->docs[0]);
    }
}
