<?php

namespace App\Support;

use Illuminate\Filesystem\Filesystem;

class CustomFilesystem extends Filesystem
{
    /**
     * Find pathnames matching a pattern.
     *
     * @param string $pattern
     * @param int $flags
     * @return array
     */
    public function glob($pattern, $flags = 0)
    {
        return glob($pattern, $flags) ?: [];
    }
} 