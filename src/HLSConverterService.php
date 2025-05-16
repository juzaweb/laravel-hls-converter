<?php
namespace Juzaweb\HLSConverter;

use Illuminate\Support\Facades\File;

class HLSConverterService implements HLSConverter
{
    public function convert(string $inputPath, string $outputDir, array $resolutions = []): void
    {
        File::ensureDirectoryExists($outputDir);

        if (empty($resolutions)) {
            $this->convertSingle($inputPath, $outputDir);
        } else {
            $this->convertMultiple($inputPath, $outputDir, $resolutions);
        }
    }

    protected function convertSingle(string $input, string $output)
    {
        File::ensureDirectoryExists($output);

        $segmentPath = $output . '/seg_%03d.ts';
        $playlistPath = $output . '/index.m3u8';

        $cmd = sprintf(
            'ffmpeg -y -i %s -c:a aac -ar 48000 -b:a 128k -c:v h264 -crf 20 -g 48 -keyint_min 48 -sc_threshold 0 -hls_time 6 -hls_playlist_type vod -hls_segment_filename %s %s',
            escapeshellarg($input),
            escapeshellarg($segmentPath),
            escapeshellarg($playlistPath)
        );

        $this->runCommand($cmd);
    }

    protected function convertMultiple(string $input, string $baseOutputDir, array $resolutions)
    {
        File::ensureDirectoryExists($baseOutputDir);

        $masterPlaylist = ["#EXTM3U"];

        foreach ($resolutions as $label => $res) {
            $variantDir = "{$baseOutputDir}/{$label}";
            File::ensureDirectoryExists($variantDir);

            $segmentPath = $variantDir . '/seg_%03d.ts';
            $playlistPath = $variantDir . '/index.m3u8';

            $cmd = sprintf(
                'ffmpeg -y -i %s -vf scale=w=%d:h=%d -c:a aac -ar 48000 -b:a 128k -c:v h264 -profile:v main -crf 20 -g 48 -keyint_min 48 -sc_threshold 0 -b:v %s -maxrate %s -bufsize 1000k -hls_time 6 -hls_playlist_type vod -hls_segment_filename %s %s',
                escapeshellarg($input),
                $res['w'],
                $res['h'],
                $res['bitrate'],
                $res['bitrate'],
                escapeshellarg($segmentPath),
                escapeshellarg($playlistPath)
            );

            $this->runCommand($cmd);

            $bandwidth = (int) filter_var($res['bitrate'], FILTER_SANITIZE_NUMBER_INT) * 1000;
            $masterPlaylist[] = "#EXT-X-STREAM-INF:BANDWIDTH={$bandwidth},RESOLUTION={$res['w']}x{$res['h']}";
            $masterPlaylist[] = "{$label}/index.m3u8";
        }

        file_put_contents("{$baseOutputDir}/master.m3u8", implode("\n", $masterPlaylist) . "\n");
    }

    protected function runCommand(string $cmd): void
    {
        exec($cmd . ' 2>&1', $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \RuntimeException("FFmpeg failed: " . implode("\n", $output));
        }
    }
}
