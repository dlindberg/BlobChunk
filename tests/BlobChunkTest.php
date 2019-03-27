<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk;

final class BlobChunkTest extends \PHPUnit\Framework\TestCase
{
    private $sampleDoc = '
<div>
    <p>First Paragraph. Does it have text? Yes, it does.</p>
</div>
<p>
    Another paragraph, not in a div.
</p>
<div class="thing">
    <h1>
        This is a title
    </h1>
    <h2>
        This is a sub title
    </h2>
    <p> Then this is some text <em> that has emphasis </em>.</p>
</div>
<table>
    <thead>
        <tr>
            <th>Col 1</th>
            <th>col 2</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>data one</td>
            <td>data one col two</td>
        </tr>
        <tr>
            <td>data two</td>
            <td>data two col two</td>
        </tr>
    </tbody>
</table>
<ul>
    <li>Item one</li>
    <li>Item Two</li>
    <li>Item Three
        <ol>
            <li>sub item</li>
            <li>sub item</li>
        </ol>
    </li>
</ul>
<dl>
    <dt>Term 1</dt>
    <dd>def 1</dd>
    <dt>Term 2</dt>
    <dd>def 2.1</dd>
    <dd>def 2.2</dd>
    <dt>term 3</dt>
    <dt>term 4</dt>
</dl>';

    public function testConstruct()
    {
        $this->assertInstanceOf(BlobChunk::class, new BlobChunk());
    }

    public function testParse()
    {
        $this->assertIsArray((new BlobChunk())->parse($this->sampleDoc));
    }

    public function testStatic()
    {
        $this->assertIsArray(BlobChunk::process($this->sampleDoc));
    }

    public function testInvocation()
    {
        $this->assertIsArray((new BlobChunk())($this->sampleDoc));
    }
}
