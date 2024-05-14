<?php

declare(strict_types=1);

namespace LinasRam\EcsGitlabFormat\Tests\Console\Output;

use LinasRam\EcsGitlabFormat\Console\Output\GitlabOutputFormatter;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Console\Output\ExitCodeResolver;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;

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
        $errorAndDiffResult = $this->createMock(ErrorAndDiffResult::class);
        $configuration = $this->createMock(Configuration::class);

        $this->easyCodingStandardStyle->expects($this->once())->method('writeln');
        $this->exitCodeResolver->expects($this->once())->method('resolve');

        $this->gitlabOutputFormatter->report($errorAndDiffResult, $configuration);
    }

    public function testReportGenerationWithoutErrors(): void
    {
        $errorAndDiffResult = $this->createMock(ErrorAndDiffResult::class);
        $configuration = $this->createMock(Configuration::class);

        $this->easyCodingStandardStyle->expects($this->once())->method('writeln');
        $this->exitCodeResolver->expects($this->once())->method('resolve');

        $this->gitlabOutputFormatter->report($errorAndDiffResult, $configuration);
    }

    public function testGetName(): void
    {
        $this->assertEquals(GitlabOutputFormatter::NAME, $this->gitlabOutputFormatter->getName());
    }
}
