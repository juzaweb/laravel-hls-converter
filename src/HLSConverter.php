<?php

namespace Juzaweb\HLSConverter;

interface HLSConverter
{
    public function convert(string $inputPath, string $outputDir, array $resolutions = []): void;
}
