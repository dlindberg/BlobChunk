<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Parser;

use dlindberg\BlobChunk\Manager\Config;
use dlindberg\BlobChunk\Manager\Manager;
use dlindberg\DOMDocumentFactory\DOMDocumentFactory;

final class RowColTest extends \PHPUnit\Framework\TestCase
{
    private $basicTable = '<table>
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
</table>';

    private $config = [
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
    ];
    /**
     * @var RowCol
     */
    private $rowCol;

    /**
     * @var DOMDocumentFactory
     */
    private $doc;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->rowCol = new RowCol(new Manager(new Config($this->config[0])));
        $this->doc = new DOMDocumentFactory();
    }

    public function testParse()
    {
        $node = $this->doc->getNode($this->basicTable)->firstChild;
        $this->assertInstanceOf(\DOMElement::class, $node);
        if ($node instanceof \DOMElement) {
            $result = $this->rowCol->parse($node);
            $this->assertEquals(['<thead>
        <tr>
            <th>Col 1</th>
            <th>col 2</th>
        </tr>
    </thead>'], $result[0]['thead']);
        }
    }
}
