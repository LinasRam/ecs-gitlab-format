<?php

declare(strict_types=1);

namespace LinasRam\EcsGitlabFormat\Tests\Console\Output;

use LinasRam\EcsGitlabFormat\Console\Output\GitlabOutputFormatter;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Console\Output\ExitCodeResolver;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;

/**
 * @covers \LinasRam\EcsGitlabFormat\Console\Output\GitlabOutputFormatter
 */
final class GitlabOutputFormatterTest extends TestCase
{
    private GitlabOutputFormatter $gitlabOutputFormatter;
    private EasyCodingStandardStyle $easyCodingStandardStyle;
    private ExitCodeResolver $exitCodeResolver;

    protected function setUp(): void
    {
        $this->easyCodingStandardStyle = $this->createMock(EasyCodingStandardStyle::class);
        $this->exitCodeResolver = $this->createMock(ExitCodeResolver::class);

        $this->gitlabOutputFormatter = new GitlabOutputFormatter(
            $this->easyCodingStandardStyle,
            $this->exitCodeResolver,
        );
    }

    public function testReportGenerationWithErrors(): void
    {
        $error = new CodingStandardError(
            10,
            'Error message',
            'CheckerClass',
            'path/file.php'
        );
        $fileDiff = new FileDiff(
            'path/file.php',
            'content',
            'new content',
            ['AnotherCheckerClass']
        );

        $errorAndDiffResult = $this->createMock(ErrorAndDiffResult::class);
        $errorAndDiffResult
            ->expects($this->once())
            ->method('getErrors')
            ->willReturn([$error]);
        $errorAndDiffResult
            ->expects($this->once())
            ->method('getFileDiffs')
            ->willReturn([$fileDiff]);
        $configuration = $this->createMock(Configuration::class);

        $expectedOutput = [
            [
                'type' => 'issue',
                'check_name' => 'CheckerClass',
                'description' => 'Error message',
                'categories' => ['Style'],
                'fingerprint' => md5('path/file.php' . 10 . 'CheckerClass'),
                'severity' => 'major',
                'location' => [
                    'path' => 'path/file.php',
                    'lines' => [
                        'begin' => 10,
                        'end' => 10,
                    ],
                ],
            ],
            [
                'type' => 'issue',
                'check_name' => 'AnotherCheckerClass',
                'description' => 'AnotherCheckerClass',
                'categories' => ['Style'],
                'fingerprint' => md5('path/file.php' . 0 . 'AnotherCheckerClass'),
                'severity' => 'minor',
                'location' => [
                    'path' => 'path/file.php',
                    'lines' => [
                        'begin' => 0,
                        'end' => 0,
                    ],
                ],
            ],
        ];
        $this->easyCodingStandardStyle
            ->expects($this->once())
            ->method('writeln')
            ->with(json_encode($expectedOutput, \JSON_PRETTY_PRINT));

        $expectedCode = 1;
        $this->exitCodeResolver
            ->expects($this->once())
            ->method('resolve')
            ->willReturn($expectedCode);

        $result = $this->gitlabOutputFormatter->report($errorAndDiffResult, $configuration);
        $this->assertSame($expectedCode, $result);
    }

    public function testReportGenerationWithoutErrors(): void
    {
        $errorAndDiffResult = $this->createMock(ErrorAndDiffResult::class);
        $errorAndDiffResult
            ->expects($this->once())
            ->method('getErrors')
            ->willReturn([]);
        $errorAndDiffResult
            ->expects($this->once())
            ->method('getFileDiffs')
            ->willReturn([]);
        $configuration = $this->createMock(Configuration::class);

        $this->easyCodingStandardStyle
            ->expects($this->once())
            ->method('writeln')
            ->with(json_encode([], \JSON_PRETTY_PRINT));

        $expectedCode = 0;
        $this->exitCodeResolver
            ->expects($this->once())
            ->method('resolve')
            ->willReturn($expectedCode);

        $result = $this->gitlabOutputFormatter->report($errorAndDiffResult, $configuration);
        $this->assertSame($expectedCode, $result);
    }

    public function testGetName(): void
    {
        $this->assertEquals(GitlabOutputFormatter::NAME, $this->gitlabOutputFormatter->getName());
    }
}
