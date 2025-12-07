<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

final class SetupScriptTest extends TestCase
{
    private string $workspace;

    protected function setUp(): void
    {
        $this->workspace = sys_get_temp_dir() . '/php-template-' . uniqid('', true);
        $this->ensureDir($this->workspace . '/bin');

        copy(__DIR__ . '/../bin/setup.php', $this->workspace . '/bin/setup.php');
        copy(__DIR__ . '/../README.template.md', $this->workspace . '/README.template.md');

        file_put_contents(
            $this->workspace . '/composer.json',
            json_encode(
                [
                    'name'        => 'vendor/package',
                    'description' => 'Old description',
                    'license'     => 'Apache-2.0',
                    'authors'     => [
                        ['name' => 'Old Name', 'email' => 'old@example.com'],
                    ],
                    'require' => ['php' => '>=8.1'],
                    'autoload' => ['psr-4' => ['Vendor\\Package\\' => 'src/']],
                ],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )
        );

        file_put_contents(
            $this->workspace . '/package.json',
            json_encode(
                [
                    'name'       => 'package',
                    'author'     => 'Old Name <old@example.com>',
                    'license'    => 'Apache-2.0',
                    'repository' => [],
                    'bugs'       => [],
                    'scripts'    => [],
                ],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )
        );

        file_put_contents($this->workspace . '/README.md', 'original');
    }

    protected function tearDown(): void
    {
        $this->deleteDir($this->workspace);
    }

    public function test_setup_script_populates_template_and_manifests(): void
    {
        $input = implode(PHP_EOL, [
            'My Title',
            'acme',
            'myapp',
            'Short description',
            '8.2',
            '1',
            'Alice',
            'alice@example.com',
        ]) . PHP_EOL;

        $process = proc_open(
            [PHP_BINARY, $this->workspace . '/bin/setup.php'],
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            $this->workspace
        );

        $this->assertIsResource($process);

        fwrite($pipes[0], $input);
        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        $error  = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        $this->assertSame(0, $exitCode, $error ?: $output);

        $readme = file_get_contents($this->workspace . '/README.md');
        $this->assertStringContainsString('# My Title', $readme);
        $this->assertStringContainsString('Short description', $readme);
        $this->assertStringNotContainsString('{{TITLE}}', $readme);
        $this->assertFileExists($this->workspace . '/README.original.md');

        $composer = json_decode((string) file_get_contents($this->workspace . '/composer.json'), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('acme/myapp', $composer['name']);
        $this->assertSame('Short description', $composer['description']);
        $this->assertSame('MIT', $composer['license']);
        $this->assertSame('>=8.2', $composer['require']['php']);
        $this->assertSame(['name' => 'Alice', 'email' => 'alice@example.com'], $composer['authors'][0]);
        $this->assertArrayHasKey('Acme\\Myapp\\', $composer['autoload']['psr-4']);
        $this->assertArrayHasKey('Tests\\', $composer['autoload-dev']['psr-4']);

        $package = json_decode((string) file_get_contents($this->workspace . '/package.json'), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('myapp', $package['name']);
        $this->assertSame('Alice <alice@example.com>', $package['author']);
        $this->assertSame('MIT', $package['license']);
        $this->assertSame('git+https://github.com/acme/myapp.git', $package['repository']['url']);
        $this->assertSame('https://github.com/acme/myapp/issues', $package['bugs']['url']);
    }

    private function ensureDir(string $dir): void
    {
        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new \RuntimeException("Unable to create directory: {$dir}");
        }
    }

    private function deleteDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $path = $item->getPathname();
            $item->isDir() ? rmdir($path) : unlink($path);
        }

        rmdir($dir);
    }
}

