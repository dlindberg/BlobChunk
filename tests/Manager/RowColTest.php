<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Manager;

use dlindberg\BlobChunk\Manager\Config as Config;
use dlindberg\BlobChunk\Manager\RowCol as RowCol;
use dlindberg\DOMDocumentFactory\DOMDocumentFactory;
use dlindberg\DOMDocumentFactory\DOMDocumentFactoryConfig;

final class RowColTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $tests = [
        [
            [
                'group' => 'rowCol',
                [
                    'parent'    => ['type' => 'tag', 'value' => 'table',],
                    'colDefs'   => ['type' => 'tag', 'value' => 'thead',],
                    'rowsGroup' => ['type' => 'tag', 'value' => 'tbody',],
                    'row'       => ['type' => 'tag', 'value' => 'tr',],
                ],
            ],
        ],
        [
            [
                'group' => 'rowCol',
                [
                    'parent'    => ['type' => 'attribute', 'name' => 'class', 'value' => 'a',],
                    'colDefs'   => ['type' => 'attribute', 'name' => 'class', 'value' => 'b',],
                    'rowsGroup' => ['type' => 'attribute', 'name' => 'class', 'value' => 'c',],
                    'row'       => ['type' => 'attribute', 'name' => 'class', 'value' => 'd',],
                ],
            ],
        ],
        [
            [
                'group' => 'rowCol',
                [
                    'parent'    => ['type' => 'attribute', 'name' => 'class', 'value' => 'a',],
                    'colDefs'   => ['type' => 'attribute', 'name' => 'class', 'value' => 'b',],
                    'rowsGroup' => ['type' => 'attribute', 'name' => 'class', 'value' => 'c',],
                    'row'       => ['type' => 'attribute', 'name' => 'class', 'value' => 'd',],
                ],
                [
                    'parent'    => ['type' => 'tag', 'value' => 'table',],
                    'colDefs'   => ['type' => 'tag', 'value' => 'thead',],
                    'rowsGroup' => ['type' => 'tag', 'value' => 'tbody',],
                    'row'       => ['type' => 'tag', 'value' => 'tr',],
                ],
            ],
        ],
        [
            [
                'group' => 'rowCol',
                [
                    'parent' => ['type' => 'tag', 'value' => 'table',],
                    'row'    => ['type' => 'tag', 'value' => 'tr',],
                ],
                [
                    'parent' => ['type' => 'attribute', 'name' => 'class', 'value' => 'a',],
                    'row'    => ['type' => 'attribute', 'name' => 'class', 'value' => 'd',],
                ],
            ],
        ],
        [
            [
                'group' => 'rowCol',
                [
                    'parent'    => ['type' => 'tag', 'value' => 'table',],
                    'colDefs'   => ['type' => 'tag', 'value' => 'thead',],
                    'rowsGroup' => ['type' => 'tag', 'value' => 'tbody',],
                    'row'       => ['type' => 'tag', 'value' => 'tr',],
                ],
                [
                    'parent'    => ['type' => 'attribute', 'name' => 'class', 'value' => 'a',],
                    'colDefs'   => ['type' => 'attribute', 'name' => 'class', 'value' => 'b',],
                    'rowsGroup' => ['type' => 'attribute', 'name' => 'class', 'value' => 'c',],
                    'row'       => ['type' => 'attribute', 'name' => 'class', 'value' => 'd',],
                ],
            ],
        ],
    ];

    private $samples = [
        '<table>
            <thead>
                <tr><th>test 0 0 0 </th><th>test 0 0 1</th></tr>
            </thead>
            <tbody>
                <tr><td>test 0 1 0</td><td>test 0 1 1</td></tr>
            </tbody>
        </table>',
        '<table class="a">
            <thead class="b">
                <tr class="d"><th>test 1 0 0</th><th>test 1 0 1</th></tr>
            </thead>
            <tbody class="c">
                <tr class="d"><td>test 1 1 0</td><td>test 1 1 1</td></tr>
            </tbody>
        </table>',
        '<div class="a">
            <div class="b">
                <div class="d"><div>test 2 0 0</div><div>test 2 0 1</div></div>
            </div>
            <div class="c">
                <div class="d"><div>test 2 1 0</div><div>test 2 1 1</div></div>
            </div>
        </div>',
        '<table>
            <tr><th>test 3 0 0 </th><th>test 3 0 1</th></tr>
            <tr><td>test 3 1 0</td><td>test 3 1 1</td></tr>
        </table>',
        '<table class="a">
            <tr class="d"><th>test 4 0 0</th><th>test 4 0 1</th></tr>
            <tr class="d"><td>test 4 1 0</td><td>test 4 1 1</td></tr>
        </table>',
        '<div class="a">
           <div class="d"><div>test 5 0 0</div><div>test 5 0 1</div></div>
           <div class="d"><div>test 5 1 0</div><div>test 5 1 1</div></div>
        </div>',
        '<table class="a">
            <thead class="e">
                <tr class="f"><th>test 6 0 0</th><th>test 6 0 1</th></tr>
            </thead>
            <tbody class="g">
                <tr class="h"><td>test 6 1 0</td><td>test 6 1 1</td></tr>
            </tbody>
        </table>',
    ];
    /**
     * @var \DOMElement[]
     */
    private $docs = [];

    /**
     * @var RowCol[]
     */
    private $rowCols;

    /**
     * @var DOMDocumentFactory
     */
    private $docFactory;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        \array_walk($this->tests, function ($test) {
            $this->rowCols[] = new RowCol(new Config($test));
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
        $set0 = $this->rowCols[0]->getSet($this->docs[0]);
        $set2 = $this->rowCols[2]->getSet($this->docs[0]);
        $set3 = $this->rowCols[3]->getSet($this->docs[0]);
        $set4 = $this->rowCols[4]->getSet($this->docs[0]);
        $this->assertInstanceOf(RowCol::class, $set0);
        $this->assertEquals($this->tests[0][0][0]['colDefs'], $set0->colDefs);
        $this->assertEquals($this->tests[0][0][0]['rowsGroup'], $set0->rowsGroup);
        $this->assertEquals($this->tests[0][0][0]['row'], $set0->row);
        $this->assertInstanceOf(RowCol::class, $set2);
        $this->assertEquals($this->tests[2][0][1]['colDefs'], $set2->colDefs);
        $this->assertEquals($this->tests[2][0][1]['rowsGroup'], $set2->rowsGroup);
        $this->assertEquals($this->tests[2][0][1]['row'], $set2->row);
        $this->assertInstanceOf(RowCol::class, $set3);
        $this->assertCount(0, $set3->colDefs);
        $this->assertCount(0, $set3->rowsGroup);
        $this->assertEquals($this->tests[3][0][0]['row'], $set3->row);
        $this->assertInstanceOf(RowCol::class, $set4);
        $this->assertCount(0, $set3->colDefs);
        $this->assertCount(0, $set3->rowsGroup);
        $this->assertEquals($this->tests[4][0][0]['row'], $set3->row);
    }

    public function testSample1()
    {
        $set0 = $this->rowCols[0]->getSet($this->docs[1]);
        $set1 = $this->rowCols[1]->getSet($this->docs[1]);
        $set2 = $this->rowCols[2]->getSet($this->docs[1]);
        $set3 = $this->rowCols[3]->getSet($this->docs[1]);
        $set4 = $this->rowCols[4]->getSet($this->docs[1]);
        $this->assertInstanceOf(RowCol::class, $set0);
        $this->assertEquals($this->tests[0][0][0]['colDefs'], $set0->colDefs);
        $this->assertEquals($this->tests[0][0][0]['rowsGroup'], $set0->rowsGroup);
        $this->assertEquals($this->tests[0][0][0]['row'], $set0->row);
        $this->assertInstanceOf(RowCol::class, $set1);
        $this->assertEquals($this->tests[1][0][0]['colDefs'], $set1->colDefs);
        $this->assertEquals($this->tests[1][0][0]['rowsGroup'], $set1->rowsGroup);
        $this->assertEquals($this->tests[1][0][0]['row'], $set1->row);
        $this->assertInstanceOf(RowCol::class, $set2);
        $this->assertEquals($this->tests[2][0][1]['colDefs'], $set2->colDefs);
        $this->assertEquals($this->tests[2][0][1]['rowsGroup'], $set2->rowsGroup);
        $this->assertEquals($this->tests[2][0][1]['row'], $set2->row);
        $this->assertInstanceOf(RowCol::class, $set3);
        $this->assertCount(0, $set3->colDefs);
        $this->assertCount(0, $set3->rowsGroup);
        $this->assertEquals($this->tests[3][0][1]['row'], $set3->row);
        $this->assertInstanceOf(RowCol::class, $set4);
        $this->assertCount(0, $set3->colDefs);
        $this->assertCount(0, $set3->rowsGroup);
        $this->assertEquals($this->tests[4][0][1]['row'], $set3->row);
    }

    public function testSample2()
    {
        $set1 = $this->rowCols[1]->getSet($this->docs[2]);
        $set2 = $this->rowCols[2]->getSet($this->docs[2]);
        $set3 = $this->rowCols[3]->getSet($this->docs[2]);
        $set4 = $this->rowCols[4]->getSet($this->docs[2]);
        $this->assertInstanceOf(RowCol::class, $set1);
        $this->assertEquals($this->tests[1][0][0]['colDefs'], $set1->colDefs);
        $this->assertEquals($this->tests[1][0][0]['rowsGroup'], $set1->rowsGroup);
        $this->assertEquals($this->tests[1][0][0]['row'], $set1->row);
        $this->assertInstanceOf(RowCol::class, $set2);
        $this->assertEquals($this->tests[2][0][0]['colDefs'], $set2->colDefs);
        $this->assertEquals($this->tests[2][0][0]['rowsGroup'], $set2->rowsGroup);
        $this->assertEquals($this->tests[2][0][0]['row'], $set2->row);
        $this->assertInstanceOf(RowCol::class, $set3);
        $this->assertCount(0, $set3->colDefs);
        $this->assertCount(0, $set3->rowsGroup);
        $this->assertEquals($this->tests[3][0][1]['row'], $set3->row);
        $this->assertInstanceOf(RowCol::class, $set4);
        $this->assertCount(0, $set3->colDefs);
        $this->assertCount(0, $set3->rowsGroup);
        $this->assertEquals($this->tests[4][0][1]['row'], $set3->row);
    }

    public function testSample3()
    {
        $set0 = $this->rowCols[0]->getSet($this->docs[3]);
        $set2 = $this->rowCols[2]->getSet($this->docs[3]);
        $set3 = $this->rowCols[3]->getSet($this->docs[3]);
        $set4 = $this->rowCols[4]->getSet($this->docs[3]);
        $this->assertInstanceOf(RowCol::class, $set0);
        $this->assertEquals($this->tests[0][0][0]['colDefs'], $set0->colDefs);
        $this->assertEquals($this->tests[0][0][0]['rowsGroup'], $set0->rowsGroup);
        $this->assertEquals($this->tests[0][0][0]['row'], $set0->row);
        $this->assertInstanceOf(RowCol::class, $set2);
        $this->assertEquals($this->tests[2][0][1]['colDefs'], $set2->colDefs);
        $this->assertEquals($this->tests[2][0][1]['rowsGroup'], $set2->rowsGroup);
        $this->assertEquals($this->tests[2][0][1]['row'], $set2->row);
        $this->assertInstanceOf(RowCol::class, $set3);
        $this->assertCount(0, $set3->colDefs);
        $this->assertCount(0, $set3->rowsGroup);
        $this->assertEquals($this->tests[3][0][0]['row'], $set3->row);
        $this->assertInstanceOf(RowCol::class, $set4);
        $this->assertCount(0, $set3->colDefs);
        $this->assertCount(0, $set3->rowsGroup);
        $this->assertEquals($this->tests[4][0][0]['row'], $set3->row);
    }

    public function testSample4()
    {
        $set0 = $this->rowCols[0]->getSet($this->docs[4]);
        $set1 = $this->rowCols[1]->getSet($this->docs[4]);
        $set2 = $this->rowCols[2]->getSet($this->docs[4]);
        $set3 = $this->rowCols[3]->getSet($this->docs[4]);
        $set4 = $this->rowCols[4]->getSet($this->docs[4]);
        $this->assertInstanceOf(RowCol::class, $set0);
        $this->assertEquals($this->tests[0][0][0]['colDefs'], $set0->colDefs);
        $this->assertEquals($this->tests[0][0][0]['rowsGroup'], $set0->rowsGroup);
        $this->assertEquals($this->tests[0][0][0]['row'], $set0->row);
        $this->assertInstanceOf(RowCol::class, $set1);
        $this->assertEquals($this->tests[1][0][0]['colDefs'], $set1->colDefs);
        $this->assertEquals($this->tests[1][0][0]['rowsGroup'], $set1->rowsGroup);
        $this->assertEquals($this->tests[1][0][0]['row'], $set1->row);
        $this->assertInstanceOf(RowCol::class, $set2);
        $this->assertEquals($this->tests[2][0][1]['colDefs'], $set2->colDefs);
        $this->assertEquals($this->tests[2][0][1]['rowsGroup'], $set2->rowsGroup);
        $this->assertEquals($this->tests[2][0][1]['row'], $set2->row);
        $this->assertInstanceOf(RowCol::class, $set3);
        $this->assertCount(0, $set3->colDefs);
        $this->assertCount(0, $set3->rowsGroup);
        $this->assertEquals($this->tests[3][0][1]['row'], $set3->row);
        $this->assertInstanceOf(RowCol::class, $set4);
        $this->assertCount(0, $set3->colDefs);
        $this->assertCount(0, $set3->rowsGroup);
        $this->assertEquals($this->tests[4][0][1]['row'], $set3->row);
    }

    public function testSample5()
    {
        $set1 = $this->rowCols[1]->getSet($this->docs[5]);
        $set2 = $this->rowCols[2]->getSet($this->docs[5]);
        $set3 = $this->rowCols[3]->getSet($this->docs[5]);
        $set4 = $this->rowCols[4]->getSet($this->docs[5]);
        $this->assertInstanceOf(RowCol::class, $set1);
        $this->assertEquals($this->tests[1][0][0]['colDefs'], $set1->colDefs);
        $this->assertEquals($this->tests[1][0][0]['rowsGroup'], $set1->rowsGroup);
        $this->assertEquals($this->tests[1][0][0]['row'], $set1->row);
        $this->assertInstanceOf(RowCol::class, $set2);
        $this->assertEquals($this->tests[2][0][0]['colDefs'], $set2->colDefs);
        $this->assertEquals($this->tests[2][0][0]['rowsGroup'], $set2->rowsGroup);
        $this->assertEquals($this->tests[2][0][0]['row'], $set2->row);
        $this->assertInstanceOf(RowCol::class, $set3);
        $this->assertCount(0, $set3->colDefs);
        $this->assertCount(0, $set3->rowsGroup);
        $this->assertEquals($this->tests[3][0][1]['row'], $set3->row);
        $this->assertInstanceOf(RowCol::class, $set4);
        $this->assertCount(0, $set3->colDefs);
        $this->assertCount(0, $set3->rowsGroup);
        $this->assertEquals($this->tests[4][0][1]['row'], $set3->row);
    }

    public function testSample6()
    {
        $set0 = $this->rowCols[0]->getSet($this->docs[6]);
        $set1 = $this->rowCols[1]->getSet($this->docs[6]);
        $set2 = $this->rowCols[2]->getSet($this->docs[6]);
        $set3 = $this->rowCols[3]->getSet($this->docs[6]);
        $set4 = $this->rowCols[4]->getSet($this->docs[6]);
        $this->assertInstanceOf(RowCol::class, $set0);
        $this->assertEquals($this->tests[0][0][0]['colDefs'], $set0->colDefs);
        $this->assertEquals($this->tests[0][0][0]['rowsGroup'], $set0->rowsGroup);
        $this->assertEquals($this->tests[0][0][0]['row'], $set0->row);
        $this->assertInstanceOf(RowCol::class, $set1);
        $this->assertEquals($this->tests[1][0][0]['colDefs'], $set1->colDefs);
        $this->assertEquals($this->tests[1][0][0]['rowsGroup'], $set1->rowsGroup);
        $this->assertEquals($this->tests[1][0][0]['row'], $set1->row);
        $this->assertInstanceOf(RowCol::class, $set2);
        $this->assertEquals($this->tests[2][0][1]['colDefs'], $set2->colDefs);
        $this->assertEquals($this->tests[2][0][1]['rowsGroup'], $set2->rowsGroup);
        $this->assertEquals($this->tests[2][0][1]['row'], $set2->row);
        $this->assertInstanceOf(RowCol::class, $set3);
        $this->assertCount(0, $set3->colDefs);
        $this->assertCount(0, $set3->rowsGroup);
        $this->assertEquals($this->tests[3][0][1]['row'], $set3->row);
        $this->assertInstanceOf(RowCol::class, $set4);
        $this->assertCount(0, $set3->colDefs);
        $this->assertCount(0, $set3->rowsGroup);
        $this->assertEquals($this->tests[4][0][1]['row'], $set3->row);
    }

    public function testFailureSample02()
    {
        $this->expectExceptionMessage('array_merge() expects at least 1 parameter, 0 given');
        $this->rowCols[0]->getSet($this->docs[2]);
    }

    public function testFailureSample05()
    {
        $this->expectExceptionMessage('array_merge() expects at least 1 parameter, 0 given');
        $this->rowCols[0]->getSet($this->docs[5]);
    }

    public function testFailureSample10()
    {
        $this->expectExceptionMessage('array_merge() expects at least 1 parameter, 0 given');
        $this->rowCols[1]->getSet($this->docs[0]);
    }

    public function testFailureSample13()
    {
        $this->expectExceptionMessage('array_merge() expects at least 1 parameter, 0 given');
        $this->rowCols[1]->getSet($this->docs[3]);
    }
}
