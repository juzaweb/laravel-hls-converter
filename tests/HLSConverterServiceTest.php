<?php

namespace Juzaweb\HLSConverter\Tests;

use Illuminate\Support\Facades\File;
use Juzaweb\HLSConverter\HLSConverter;

class HLSConverterServiceTest extends TestCase
{
    protected string $inputVideo;
    protected string $outputDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->inputVideo = __DIR__ . '/sample.mp4';
        $this->outputDir = storage_path('app/test-hls');

        File::deleteDirectory($this->outputDir);
        File::ensureDirectoryExists(dirname($this->inputVideo));
    }

    public function test_basic_hls_conversion()
    {
        $service = app(HLSConverter::class);

        $service->convert($this->inputVideo, $this->outputDir);

        $this->assertFileExists($this->outputDir . '/index.m3u8');
        $this->assertDirectoryExists($this->outputDir);
        $this->assertNotEmpty(glob($this->outputDir . '/seg_*.ts'));
    }

    public function test_multi_resolution_hls_conversion()
    {
        $service = app(HLSConverter::class);

        $resolutions = [
            '720p' => ['w' => 1280, 'h' => 720, 'bitrate' => '2000k'],
            '480p' => ['w' => 854,  'h' => 480, 'bitrate' => '1000k'],
        ];

        $service->convert($this->inputVideo, $this->outputDir, $resolutions);

        $this->assertFileExists($this->outputDir . '/master.m3u8');
        $this->assertFileExists($this->outputDir . '/720p/index.m3u8');
        $this->assertFileExists($this->outputDir . '/480p/index.m3u8');

        $this->assertNotEmpty(glob($this->outputDir . '/720p/seg_*.ts'));
        $this->assertNotEmpty(glob($this->outputDir . '/480p/seg_*.ts'));
    }
}
