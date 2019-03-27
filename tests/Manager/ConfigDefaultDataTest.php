<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Manager;

use dlindberg\BlobChunk\Manager\Config as Config;

final class ConfigDefaultDataTest extends \PHPUnit\Framework\TestCase
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

    public function testHasAttribute()
    {
        $this->assertFalse((new config($this->testData))
            ->hasAttribute('parentAttributes', ['name' => 'id', 'value' => "main"]));
        $this->assertFalse((new config($this->testData))
            ->hasAttribute('badName', ['name' => 'id', 'value' => "main"]));
    }

    public function testParents()
    {
        $this->assertContains('div', (new config($this->testData))->parentTags);
        $this->assertCount(1, (new config($this->testData))->parentTags);
        $this->assertCount(0, (new config($this->testData))->parentAttributes);
    }

    public function testSpecials()
    {
        $this->assertContains('h1', (new config($this->testData))->specialTags);
        $this->assertContains('h2', (new config($this->testData))->specialTags);
        $this->assertContains('h3', (new config($this->testData))->specialTags);
        $this->assertContains('h4', (new config($this->testData))->specialTags);
        $this->assertContains('h5', (new config($this->testData))->specialTags);
        $this->assertContains('h6', (new config($this->testData))->specialTags);
        $this->assertContains('strong', (new config($this->testData))->specialTags);
        $this->assertContains('em', (new config($this->testData))->specialTags);
        $this->assertContains('b', (new config($this->testData))->specialTags);
        $this->assertContains('i', (new config($this->testData))->specialTags);
        $this->assertContains('a', (new config($this->testData))->specialTags);
        $this->assertCount(11, (new config($this->testData))->specialTags);
        $this->assertCount(0, (new config($this->testData))->specialAttributes);
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
        $this->assertCount(0, (new config($this->testData))->recursionChildrenAttributes);
    }

    public function testSplitWhat()
    {
        $this->assertContains('p', (new config($this->testData))->splitWhatTags);
        $this->assertCount(1, (new config($this->testData))->splitWhatTags);
        $this->assertCount(0, (new config($this->testData))->splitWhatAttributes);
    }

    public function testSplitOn()
    {
        $this->assertContains('. ', (new config($this->testData))->splitOn);
        $this->assertContains('? ', (new config($this->testData))->splitOn);
        $this->assertContains('! ', (new config($this->testData))->splitOn);
        $this->assertCount(3, (new config($this->testData))->splitOn);
    }

    public function testPairParents()
    {
        $this->assertContains('dl', (new config($this->testData))->pairParentTags);
        $this->assertCount(1, (new config($this->testData))->pairParentTags);
        $this->assertCount(0, (new config($this->testData))->pairParentAttributes);
    }

    public function testPairSets()
    {
        $this->assertCount(1, (new config($this->testData))->pairSets);
        $this->assertEquals($this->testData[4][0], (new config($this->testData))->pairSets[0]);
    }

    public function testRowColParents()
    {
        $this->assertContains('table', (new config($this->testData))->rowColParentTags);
        $this->assertCount(1, (new config($this->testData))->rowColParentTags);
        $this->assertCount(0, (new config($this->testData))->rowColParentAttributes);
    }

    public function testRowColSets()
    {
        $this->assertCount(1, (new config($this->testData))->rowColSets);
        $this->assertEquals($this->testData[2][0], (new config($this->testData))->rowColSets[0]);
    }
}
