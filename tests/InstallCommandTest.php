<?php

declare(strict_types=1);

use function Pest\Laravel\artisan;

it('scaffolds the robots txt service provider', function (): void {
    $providerPath = app_path('Providers/RobotsTxtServiceProvider.php');

    if (file_exists($providerPath)) {
        unlink($providerPath);
    }

    artisan('robots:install')
        ->assertSuccessful()
    ;

    expect(file_exists($providerPath))->toBeTrue()
        ->and(file_get_contents($providerPath))->toContain('Seo\\Robots\\Laravel\\Providers\\RobotsTxtServiceProvider')
    ;
});

it('refuses to overwrite an existing provider without force', function (): void {
    $providerPath = app_path('Providers/RobotsTxtServiceProvider.php');
    $directory = dirname($providerPath);

    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    file_put_contents($providerPath, '<?php // existing');

    artisan('robots:install')
        ->assertFailed()
    ;

    expect(file_get_contents($providerPath))->toBe('<?php // existing');
});

it('overwrites an existing provider with force', function (): void {
    $providerPath = app_path('Providers/RobotsTxtServiceProvider.php');
    $directory = dirname($providerPath);

    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    file_put_contents($providerPath, '<?php // existing');

    artisan('robots:install', ['--force' => true])
        ->assertSuccessful()
    ;

    expect(file_get_contents($providerPath))->toContain('Seo\\Robots\\Laravel\\Providers\\RobotsTxtServiceProvider');
});
