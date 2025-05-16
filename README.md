# Laravel HLS Converter

[![Tests](https://github.com/juzaweb/laravel-hls-converter/actions/workflows/run-tests.yml/badge.svg)](https://github.com/juzaweb/laravel-hls-converter/actions)
[![License](https://img.shields.io/github/license/juzaweb/laravel-hls-converter.svg)](LICENSE)

A Laravel-friendly package to convert video files to HLS format (`.m3u8` + `.ts`), with support for multiple resolutions. Built on top of [FFMpeg](https://ffmpeg.org) and [Laravel](https://laravel.com).

---

## 🚀 Features

- Convert a video to HLS format
- Support multiple quality outputs (adaptive bitrate streaming)
- Automatically generate `master.m3u8`
- Laravel-friendly structure with job support

---

## 🧰 Requirements

- PHP 8.1 or higher
- Laravel 9 / 10 / 11
- [FFmpeg](https://ffmpeg.org/) installed on the server

Install FFmpeg:

```bash
sudo apt-get install ffmpeg
```

## 📦 Installation Package

```bash
composer require juzaweb/laravel-hls-converter
```

## 📝 Usage

### Basic HLS Conversion

```php
use Juzaweb\HLSConverter;

$input = storage_path('app/videos/sample.mp4');
$output = storage_path('app/hls/single');

(new HLSConverter())->convert($input, $output);
```

### Convert with multiple resolutions

```php
use Juzaweb\HLSConverter;

$input = storage_path('app/videos/sample.mp4');
$output = storage_path('app/hls/multi');

$resolutions = [
    '360p' => ['w' => 640,  'h' => 360,  'bitrate' => '800k'],
    '480p' => ['w' => 854,  'h' => 480,  'bitrate' => '1200k'],
    '720p' => ['w' => 1280, 'h' => 720,  'bitrate' => '2500k'],
];

(new HLSConverter())->convert($input, $output, $resolutions);
```

This will generate:
```
output/
├── 360p/
│   ├── seg_000.ts
│   └── index.m3u8
├── 480p/
│   └── ...
├── 720p/
│   └── ...
└── master.m3u8
```

## 🧪 Running Tests

```bash
composer test
```

### 📁 Example Laravel Job

```php
use Juzaweb\HLSConverter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ConvertVideoToHLSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 120;

    public function __construct(
        protected string $input,
        protected string $output,
        protected ?array $resolutions = null
    ) {}

    public function handle()
    {
        (new HLSConverter())->convert($this->input, $this->output, $this->resolutions);
    }
}
```

## 📝 License

The HLS Converter is released under the [MIT License](LICENSE.md).
