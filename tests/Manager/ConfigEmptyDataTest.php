<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Manager;

use dlindberg\BlobChunk\Manager\Config as Config;

final class ConfigEmptyDataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $testDataAllEmpty = [];

    /**
     * @var array
     */
    private $testDataEmptySegments = [
        ['group' => 'parents', 'tags' => [], 'attributes' => [],],
        ['group' => 'specials', 'tags' => [], 'attributes' => [],],
        ['group' => 'rowCol', ['parent' => [], 'colDefs' => [], 'rowsGroup' => [], 'row' => [],], [],],
        ['group' => 'recursive', 'parents' => [], 'children' => ['tags' => [], 'attributes' => null,],],
        ['group' => 'pairs', [],],
        ['group' => 'splits', 'what' => [], 'on' => [],],
    ];

    public function testHasAttribute()
    {
        $this->assertFalse((new config($this->testDataEmptySegments))
            ->hasAttribute('parentAttributes', ['name' => 'id', 'value' => "main"]));
        $this->assertFalse((new config($this->testDataAllEmpty))
            ->hasAttribute('parents', ['name' => 'id', 'value' => "main"]));
        $this->assertFalse((new config($this->testDataEmptySegments))
            ->hasAttribute('badName', ['name' => 'id', 'value' => "main"]));
        $this->assertFalse((new config($this->testDataAllEmpty))
            ->hasAttribute('badName', ['name' => 'id', 'value' => "main"]));
    }

    public function testParents()
    {
        $this->assertNotContains('div', (new config($this->testDataEmptySegments))->parentTags);
        $this->assertNotContains('div', (new config($this->testDataAllEmpty))->parentTags);
        $this->assertNotContains('section', (new config($this->testDataEmptySegments))->parentTags);
        $this->assertNotContains('section', (new config($this->testDataAllEmpty))->parentTags);
        $this->assertCount(0, (new config($this->testDataAllEmpty))->parentTags);
        $this->assertCount(0, (new config($this->testDataAllEmpty))->parentAttributes);
        $this->assertCount(0, (new config($this->testDataEmptySegments))->parentTags);
        $this->assertCount(0, (new config($this->testDataEmptySegments))->parentAttributes);
    }

    public function testSpecials()
    {
        $this->assertCount(0, (new config($this->testDataEmptySegments))->specialTags);
        $this->assertCount(0, (new config($this->testDataAllEmpty))->specialTags);
        $this->assertCount(0, (new config($this->testDataEmptySegments))->specialAttributes);
        $this->assertCount(0, (new config($this->testDataAllEmpty))->specialAttributes);
    }

    public function testRecursionParents()
    {
        $this->assertCount(0, (new config($this->testDataEmptySegments))->recursionParentTags);
        $this->assertCount(0, (new config($this->testDataEmptySegments))->recursionParentAttributes);
        $this->assertCount(0, (new config($this->testDataAllEmpty))->recursionParentTags);
        $this->assertCount(0, (new config($this->testDataAllEmpty))->recursionParentAttributes);
    }

    public function testRecursionChildren()
    {
        $this->assertCount(0, (new config($this->testDataEmptySegments))->recursionChildrenTags);
        $this->assertCount(0, (new config($this->testDataEmptySegments))->recursionChildrenAttributes);
        $this->assertCount(0, (new config($this->testDataAllEmpty))->recursionChildrenTags);
        $this->assertCount(0, (new config($this->testDataAllEmpty))->recursionChildrenAttributes);
    }

    public function testSplitWhat()
    {
        $this->assertCount(0, (new config($this->testDataEmptySegments))->splitWhatTags);
        $this->assertCount(0, (new config($this->testDataEmptySegments))->splitWhatAttributes);
        $this->assertCount(0, (new config($this->testDataAllEmpty))->splitWhatTags);
        $this->assertCount(0, (new config($this->testDataAllEmpty))->splitWhatAttributes);
    }

    public function testSplitOn()
    {
        $this->assertCount(0, (new config($this->testDataAllEmpty))->splitOn);
        $this->assertCount(0, (new config($this->testDataEmptySegments))->splitWhatAttributes);
    }

    public function testPairParents()
    {
        $this->assertCount(0, (new config($this->testDataEmptySegments))->pairParentTags);
        $this->assertCount(0, (new config($this->testDataEmptySegments))->pairParentAttributes);
        $this->assertCount(0, (new config($this->testDataAllEmpty))->pairParentTags);
        $this->assertCount(0, (new config($this->testDataAllEmpty))->pairParentAttributes);
    }

    public function testPairSets()
    {
        $this->assertCount(0, (new config($this->testDataAllEmpty))->pairSets);
        $this->assertCount(0, (new config($this->testDataEmptySegments))->pairSets);
    }

    public function testRowColParents()
    {
        $this->assertCount(0, (new config($this->testDataEmptySegments))->rowColParentTags);
        $this->assertCount(0, (new config($this->testDataEmptySegments))->rowColParentAttributes);
        $this->assertCount(0, (new config($this->testDataAllEmpty))->rowColParentTags);
        $this->assertCount(0, (new config($this->testDataAllEmpty))->rowColParentAttributes);
    }

    public function testRowColSets()
    {
        $this->assertCount(0, (new config($this->testDataAllEmpty))->rowColSets);
        $this->assertCount(0, (new config($this->testDataEmptySegments))->rowColSets);
    }
}
