<?php

namespace Modules\Media\Services;

use Illuminate\Support\Facades\Config;

class DiskService
{
 
    /**
     * Add a disk configuration to the filesystems.
     *
     * @param string $diskName
     * @param string $rootPath
     */
    public function addDisk(string $diskName, string $rootPath)
    {
        $diskConfig = [
            'driver' => 'local',
            'root' => $rootPath,
        ];

        // Merge the new disk with the existing disks in the configuration
        Config::set("filesystems.disks.{$diskName}", $diskConfig);
    }
}
