<?php

declare(strict_types=1);

namespace LinasRam\EcsGitlabFormat\Tests\Console\Output;

use LinasRam\EcsGitlabFormat\Console\Output\GitlabOutputFormatter;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;

/**
 * @covers \LinasRam\EcsGitlabFormat\Console\Output\GitlabOutputFormatter
 */
final class GitlabOutputFormatterTest extends TestCase
{
    private EasyCodingStandardStyle $easyCodingStandardStyle;
    private GitlabOutputFormatter $gitlabOutputFormatter;

    protected function setUp(): void
    {
        $this->easyCodingStandardStyle = $this->createMock(EasyCodingStandardStyle::class);

        $this->gitlabOutputFormatter = new GitlabOutputFormatter($this->easyCodingStandardStyle);
    }

    public function testReportGenerationWithErrors(): void
    {
        $errorAndDiffResult = $this->createMock(ErrorAndDiffResult::class);
        $configuration = $this->createMock(Configuration::class);

        $this->easyCodingStandardStyle->expects($this->once())->method('writeln');

        $this->gitlabOutputFormatter->report($errorAndDiffResult, $configuration);
    }

    public function testReportGenerationWithoutErrors(): void
    {
        $errorAndDiffResult = $this->createMock(ErrorAndDiffResult::class);
        $configuration = $this->createMock(Configuration::class);

        $this->easyCodingStandardStyle->expects($this->once())->method('writeln');

        $this->gitlabOutputFormatter->report($errorAndDiffResult, $configuration);
    }

    public function testGetName(): void
    {
        $this->assertEquals(GitlabOutputFormatter::NAME, $this->gitlabOutputFormatter->getName());
    }
}
