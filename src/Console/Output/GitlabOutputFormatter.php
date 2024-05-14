<?php

declare(strict_types=1);

namespace LinasRam\EcsGitlabFormat\Console\Output;

use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;

class GitlabOutputFormatter implements OutputFormatterInterface
{
    public const NAME = 'gitlab';
    private const SEVERITY_MAJOR = 'major';
    private const SEVERITY_MINOR = 'minor';
    private const SUCCESS = 0;
    private const FAILURE = 1;

    private EasyCodingStandardStyle $easyCodingStandardStyle;

    public function __construct(EasyCodingStandardStyle $easyCodingStandardStyle)
    {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }

    public function report($errorAndDiffResult, $configuration): int
    {
        $report = $this->generateReport($errorAndDiffResult);
        $this->easyCodingStandardStyle->writeln(json_encode($report, \JSON_PRETTY_PRINT));

        return empty($report) ? self::SUCCESS : self::FAILURE;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function generateReport(ErrorAndDiffResult $errorAndDiffResult): array
    {
        $report = [];

        foreach ($errorAndDiffResult->getErrors() as $error) {
            $report[] = $this->buildGitlabIssue(
                $error->getCheckerClass(),
                $error->getMessage(),
                $error->getRelativeFilePath(),
                $error->getLine(),
                self::SEVERITY_MAJOR,
            );
        }

        foreach ($errorAndDiffResult->getFileDiffs() as $fileDiff) {
            foreach ($fileDiff->getAppliedCheckers() as $checker) {
                $report[] = $this->buildGitlabIssue(
                    $checker,
                    $checker,
                    $fileDiff->getRelativeFilePath(),
                    0,
                    self::SEVERITY_MINOR,
                );
            }
        }

        return $report;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildGitlabIssue(
        string $checkName,
        string $description,
        string $filePath,
        int $line,
        string $severity
    ): array {
        return [
            'type' => 'issue',
            'check_name' => $checkName,
            'description' => $description,
            'categories' => ['Style'],
            'fingerprint' => md5($filePath . $line . $checkName),
            'severity' => $severity,
            'location' => [
                'path' => $filePath,
                'lines' => [
                    'begin' => $line,
                    'end' => $line,
                ],
            ],
        ];
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
