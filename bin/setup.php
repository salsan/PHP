#!/usr/bin/env php
<?php

declare(strict_types=1);

(function (): void {
    $color = function (string $text, string $code): string {
        static $useColors = null;
        if ($useColors === null) {
            $useColors = function_exists('stream_isatty')
                ? @stream_isatty(STDOUT)
                : (PHP_OS_FAMILY !== 'Windows');
        }
        return $useColors ? "\033[{$code}m{$text}\033[0m" : $text;
    };

    $ask = function (string $question, ?string $default = null) use ($color): string {
        $suffix = $default !== null ? " [$default]" : "";
        echo $color("?> ", "0;35") . $question . $suffix . ": ";
        $answer = trim(fgets(STDIN) ?: "");
        return ($answer === "" && $default !== null) ? $default : $answer;
    };

    $gitConfig = function (string $key): ?string {
        $output = [];
        $exitCode = 0;
        @exec('git config --get ' . escapeshellarg($key), $output, $exitCode);
        if ($exitCode === 0 && isset($output[0])) {
            $value = trim($output[0]);
            return $value !== '' ? $value : null;
        }
        return null;
    };

    $projectRoot = realpath(__DIR__ . "/..");
    if ($projectRoot === false) {
        echo $color("[ERR] Cannot determine project root", "0;31") . PHP_EOL;
        exit(1);
    }

    $templateFile = $projectRoot . "/README.template.md";
    $readmeFile   = $projectRoot . "/README.md";
    $backupFile   = $projectRoot . "/README.original.md";

    if (!file_exists($templateFile)) {
        echo $color("[ERR] README.template.md not found in project root", "0;31") . PHP_EOL;
        exit(1);
    }

    echo $color("=== PHP TEMPLATE README SETUP ===", "1;34") . PHP_EOL . PHP_EOL;

    $defaultVendor   = "salsan";
    $defaultName     = basename($projectRoot);
    $defaultTitle    = "PHP Template";
    $defaultDesc     = "A minimal and flexible PHP template to kickstart your PHP projects, "
        . "including development tools for linting, testing, and ensuring code quality.";
    $defaultPhp      = "8.1.10";
    $defaultLicense  = "MIT";

    $gitName  = $gitConfig('user.name');
    $gitEmail = $gitConfig('user.email');

    $title       = $ask("Project title", $defaultTitle);
    $vendor      = $ask("Vendor / GitHub user or org", $defaultVendor);
    $packageName = $ask("Repository / package name", $defaultName);
    $description = $ask("Short description", $defaultDesc);
    $phpVersion  = $ask("Minimum PHP version", $defaultPhp);

    // -----------------------------------------------------------------------------
    // License: show available, then ask
    // -----------------------------------------------------------------------------

    $licenseLinks = [
        "MIT"          => "https://opensource.org/licenses/MIT",
        "Apache-2.0"   => "https://www.apache.org/licenses/LICENSE-2.0",
        "GPL-3.0"      => "https://www.gnu.org/licenses/gpl-3.0.en.html",
        "LGPL-3.0"     => "https://www.gnu.org/licenses/lgpl-3.0.en.html",
        "BSD-3-Clause" => "https://opensource.org/licenses/BSD-3-Clause",
        "BSD-2-Clause" => "https://opensource.org/licenses/BSD-2-Clause",
        "MPL-2.0"      => "https://www.mozilla.org/en-US/MPL/2.0/",
        "Unlicense"    => "https://unlicense.org/",
    ];

    echo PHP_EOL . $color("Available Licenses:", "1;36") . PHP_EOL;

    $i = 1;
    $licenseKeys = array_keys($licenseLinks);

    foreach ($licenseLinks as $name => $url) {
        $num = str_pad((string)$i, 2, " ", STR_PAD_LEFT);
        echo $color("[$num]", "0;33") . " $name → $url" . PHP_EOL;
        $i++;
    }

    echo PHP_EOL;

    $licenseInput = $ask("Select license by number or SPDX", $defaultLicense);

    // If user chooses a numeric index, map it to a known license key
    if (ctype_digit($licenseInput) && isset($licenseKeys[(int)$licenseInput - 1])) {
        $license = $licenseKeys[(int)$licenseInput - 1];
    } else {
        // Otherwise, treat it as raw SPDX string
        $license = $licenseInput;
    }

    $authorName  = $ask("Author (full name)", $gitName ?? "PHP Developer");
    $authorEmail = $ask("Author email", $gitEmail ?? "php@localhost");

    $licenseUrl = $licenseLinks[$license] ?? "https://spdx.org/licenses/{$license}.html";

    // -----------------------------------------------------------------------------
    // Badge values for Shields.io
    // {{LICENSE_BADGE}}         → versioned, compatible with Shields (GPL-3.0 → GPL--3.0)
    // {{LICENSE_BADGE_SIMPLE}}  → only alphabetic (GPL-3.0 → GPL)
    // -----------------------------------------------------------------------------

    // Versioned badge (compatible with path-based Shields format)
    $licenseBadge = str_replace('-', '--', $license);

    // Simple badge label with only letters (e.g. "GPL" from "GPL-3.0")
    $licenseBadgeSimple = preg_replace('/[^A-Za-z]/', '', $license);

    $replacements = [
        "{{TITLE}}"               => $title,
        "{{VENDOR}}"              => $vendor,
        "{{NAME}}"                => $packageName,
        "{{DESCRIPTION}}"         => $description,
        "{{PHP_VERSION}}"         => $phpVersion,
        "{{LICENSE}}"             => $license,
        "{{LICENSE_URL}}"         => $licenseUrl,
        "{{LICENSE_BADGE}}"       => $licenseBadge,
        "{{LICENSE_BADGE_SIMPLE}}" => $licenseBadgeSimple,
        "{{AUTHOR_NAME}}"         => $authorName,
        "{{AUTHOR_EMAIL}}"        => $authorEmail,
        "{{YEAR}}"                => date("Y"),
    ];

    if (file_exists($readmeFile) && !file_exists($backupFile)) {
        if (@rename($readmeFile, $backupFile)) {
            echo $color("[OK] README.md backed up to README.original.md", "0;32") . PHP_EOL;
        } else {
            echo $color("[WARN] Could not backup README.md to README.original.md", "1;33") . PHP_EOL;
        }
    }

    $templateContent = file_get_contents($templateFile);
    if ($templateContent === false) {
        echo $color("[ERR] Could not read README.template.md", "0;31") . PHP_EOL;
        exit(1);
    }

    $newContent = str_replace(
        array_keys($replacements),
        array_values($replacements),
        $templateContent
    );

    if (file_put_contents($readmeFile, $newContent) === false) {
        echo $color("[ERR] Could not write README.md", "0;31") . PHP_EOL;
        exit(1);
    }

    echo PHP_EOL . $color("[OK] README.md generated successfully", "0;32") . PHP_EOL;
    echo $color("Done.", "1;32") . PHP_EOL;
})();
