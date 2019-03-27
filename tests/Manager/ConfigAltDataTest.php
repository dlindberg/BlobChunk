<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Manager;

use dlindberg\BlobChunk\Manager\Config as Config;

final class ConfigAltDataTest extends \PHPUnit\Framework\TestCase
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
            'tags'       => ['strong', 'em'],
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
                'tags'       => ['ul', 'ol',],
                'attributes' => [],
            ],
            'children' => [
                'tags'       => ['li',],
                'attributes' => [['name' => 'class', 'value' => 'item']],
            ],
        ],
        [
            'group' => 'pairs',
            [
                'parent' => ['type' => 'tag', 'value' => 'dl',],
                'a'      => ['type' => 'tag', 'value' => 'dt',],
                'b'      => ['type' => 'tag', 'value' => 'dd',],
            ],
        ],
        [
            'group' => 'splits',
            'what'  => [
                'tags'       => ['p'],
                'attributes' => [
                    ['name' => 'class', 'value' => 'chunk'],
                    ['name' => 'id', 'value' => 'blob'],
                ],
            ],
            'on'    => ['. ', '—',],
        ],
    ];

    public function testHasAttribute()
    {
        $this->assertTrue((new config($this->testData))
            ->hasAttribute('parentAttributes', ['name' => 'id', 'value' => "main"]));
        $this->assertTrue((new config($this->testData))
            ->hasAttribute('parents', ['name' => 'id', 'value' => "main"]));
        $this->assertFalse((new config($this->testData))
            ->hasAttribute('badName', ['name' => 'id', 'value' => "main"]));
    }

    public function testParents()
    {
        $this->assertNotContains('div', (new config($this->testData))->parentTags);
        $this->assertNotContains('section', (new config($this->testData))->parentTags);
        $this->assertTrue((new config($this->testData))
            ->hasAttribute('parentAttributes', ['name' => 'id', 'value' => "main"]));
        $this->assertTrue((new config($this->testData))
            ->hasAttribute('parentAttributes', ['name' => 'id', 'value' => "secondary"]));
        $this->assertCount(0, (new config($this->testData))->parentTags);
        $this->assertCount(2, (new config($this->testData))->parentAttributes);
    }

    public function testSpecials()
    {
        $this->assertNotContains('h1', (new config($this->testData))->specialTags);
        $this->assertNotContains('h2', (new config($this->testData))->specialTags);
        $this->assertNotContains('h3', (new config($this->testData))->specialTags);
        $this->assertNotContains('h4', (new config($this->testData))->specialTags);
        $this->assertNotContains('h5', (new config($this->testData))->specialTags);
        $this->assertNotContains('h6', (new config($this->testData))->specialTags);
        $this->assertContains('strong', (new config($this->testData))->specialTags);
        $this->assertContains('em', (new config($this->testData))->specialTags);
        $this->assertNotContains('b', (new config($this->testData))->specialTags);
        $this->assertNotContains('i', (new config($this->testData))->specialTags);
        $this->assertNotContains('a', (new config($this->testData))->specialTags);
        $this->assertCount(2, (new config($this->testData))->specialTags);
        $this->assertCount(4, (new config($this->testData))->specialAttributes);
        $this->assertTrue((new config($this->testData))
            ->hasAttribute('specialAttributes', ['name' => 'id', 'value' => "header"]));
        $this->assertTrue((new config($this->testData))
            ->hasAttribute('specialAttributes', ['name' => 'id', 'value' => "deck"]));
        $this->assertTrue((new config($this->testData))
            ->hasAttribute('specialAttributes', ['name' => 'class', 'value' => "pull"]));
        $this->assertTrue((new config($this->testData))
            ->hasAttribute('specialAttributes', ['name' => 'class', 'value' => "bem_mode"]));
    }

    public function testRecursionParents()
    {
        $this->assertContains('ul', (new config($this->testData))->recursionParentTags);
        $this->assertContains('ol', (new config($this->testData))->recursionParentTags);
        $this->assertCount(2, (new config($this->testData))->recursionParentTags);
        $this->assertCount(0, (new config($this->testData))->recursionParentAttributes);
    }

    public function testRecursionChildren()
    {
        $this->assertContains('li', (new config($this->testData))->recursionChildrenTags);
        $this->assertCount(1, (new config($this->testData))->recursionChildrenTags);
        $this->assertCount(1, (new config($this->testData))->recursionChildrenAttributes);
        $this->assertTrue((new config($this->testData))
            ->hasAttribute('recursionChildren', ['name' => 'class', 'value' => "item"]));
    }

    public function testSplitWhat()
    {
        $this->assertContains('p', (new config($this->testData))->splitWhatTags);
        $this->assertCount(1, (new config($this->testData))->splitWhatTags);
        $this->assertCount(2, (new config($this->testData))->splitWhatAttributes);
        $this->assertTrue((new config($this->testData))
            ->hasAttribute('splitWhat', ['name' => 'class', 'value' => "chunk"]));
        $this->assertTrue((new config($this->testData))
            ->hasAttribute('splitWhat', ['name' => 'id', 'value' => "blob"]));
    }

    public function testSplitOn()
    {
        $this->assertContains('. ', (new config($this->testData))->splitOn);
        $this->assertNotContains('? ', (new config($this->testData))->splitOn);
        $this->assertNotContains('! ', (new config($this->testData))->splitOn);
        $this->assertContains('—', (new config($this->testData))->splitOn);
        $this->assertCount(2, (new config($this->testData))->splitOn);
    }

    public function testPairParents()
    {
        $this->assertContains('dl', (new config($this->testData))->pairParentTags);
        $this->assertCount(1, (new config($this->testData))->pairParentTags);
        $this->assertCount(0, (new config($this->testData))->pairParentAttributes);
    }

    public function testPairSets()
    {
        $this->assertContains('dl', (new config($this->testData))->pairParentTags);
        $this->assertCount(1, (new config($this->testData))->pairParentTags);
        $this->assertCount(0, (new config($this->testData))->pairParentAttributes);
    }

    public function testRowColParents()
    {
        $this->assertCount(1, (new config($this->testData))->pairSets);
        $this->assertEquals($this->testData[4][0], (new config($this->testData))->pairSets[0]);
    }

    public function testRowColSets()
    {
        $this->assertCount(1, (new config($this->testData))->rowColSets);
        $this->assertEquals($this->testData[2][0], (new config($this->testData))->rowColSets[0]);
    }
}
