# GitLab output formatter for easy-coding-standard.

A [GitLab](https://gitlab.com) output formatter
for [easy-coding-standard](https://github.com/easy-coding-standard/easy-coding-standard).
Generates code quality report in GitLab Code Quality format, so, you can see it in GitLab UI.

## Installation

Install the package via Composer:

```bash
composer require linasram/ecs-gitlab-format --dev
```

Configure your `ecs.php` file to use the formatter:

```php
<?php

use LinasRam\EcsGitlabFormat\Console\Output\GitlabOutputFormatter;
// ...

return static function (ContainerConfigurator $containerConfigurator): void {
    // ...

    $containerConfigurator->services()->set(GitlabOutputFormatter::class);
};
```

## Usage

Run the `ecs` command with the `--output-format=gitlab` option.

```bash
vendor/bin/ecs --output-format=gitlab
```

## GitLab CI

Include the following job in your `.gitlab-ci.yml` file:

```yaml
ecs:
    script:
        - vendor/bin/ecs --output-format=gitlab > ecs-quality-report.json
    artifacts:
        reports:
            codequality: ecs-quality-report.json
```
