<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

use function App\search;

class SearchTest extends TestCase
{
    protected array $docs;

    protected function setUp(): void
    {
        $doc1 = "I can't shoot straight unless I've had a pint!";
        $doc2 = "Don't shoot shoot shoot that thing at me.";
        $doc3 = "I'm your shooter.";

        $this->docs = [
            ['id' => 'doc1', 'text' => $doc1],
            ['id' => 'doc2', 'text' => $doc2],
            ['id' => 'doc3', 'text' => $doc3],
        ];
    }

    public function testSearch(): void
    {
        $results = search($this->docs, 'shoot');
        $this->assertCount(2, $results);
        $this->assertEquals('doc1', $results[0]);
        $this->assertEquals('doc2', $results[1]);
    }

    public function testNotFound(): void
    {
        $results = search($this->docs, 'notfound');
        $this->assertCount(0, $results);
    }

    public function testEmptyDocs(): void
    {
        $results = search([], 'shoot');
        $this->assertCount(0, $results);
    }

    public function testSearchWithSpecialCharacters(): void
    {
        $results = search($this->docs, 'pint!');
        $this->assertCount(1, $results);
    }
}
