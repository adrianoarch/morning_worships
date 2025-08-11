<?php

namespace Tests\Unit;

use App\Traits\VttTextExtractor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class VttTextExtractorTest extends TestCase
{
    use VttTextExtractor;

    #[Test]
    public function it_can_extract_text_from_a_simple_vtt_content(): void
    {
        $vttContent = <<<VTT
WEBVTT

1
00:00:01.000 --> 00:00:04.000
Hello world.

2
00:00:05.000 --> 00:00:08.000
This is a test.
VTT;

        $expectedText = "Hello world. This is a test.";

        $extractedText = $this->extractTextFromVtt($vttContent);

        $this->assertEquals($expectedText, $extractedText);
    }

    #[Test]
    public function it_strips_html_tags_from_the_content(): void
    {
        $vttContent = <<<VTT
WEBVTT

00:00:01.000 --> 00:00:04.000
<i>Hello</i> <b>world</b>.

00:00:05.000 --> 00:00:08.000
This is a <u>test</u> with tags.
VTT;

        $expectedText = "Hello world. This is a test with tags.";

        $extractedText = $this->extractTextFromVtt($vttContent);

        $this->assertEquals($expectedText, $extractedText);
    }

    #[Test]
    #[DataProvider('emptyContentProvider')]
    public function it_handles_various_empty_or_invalid_inputs(string $vttContent): void
    {
        $extractedText = $this->extractTextFromVtt($vttContent);
        $this->assertEmpty($extractedText);
    }

    public static function emptyContentProvider(): array
    {
        return [
            'empty string' => [''],
            'only header' => ['WEBVTT'],
            'header and timestamp' => ["WEBVTT\n00:00:01.000 --> 00:00:04.000"],
            'only timestamp' => ["00:00:01.000 --> 00:00:04.000"],
            'only sequence numbers' => ["1\n2\n3"],
        ];
    }
}
