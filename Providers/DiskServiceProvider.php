<?php

namespace Modules\Media\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Media\Services\DiskService;

class DiskServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Extend the filesystem configuration when the app boots
        $diskService = app(DiskService::class);
    
        // Step 1: Add the 'images' disk
        $diskService->addDisk('images', storage_path('app/uploads/images'));
        $diskService->addDisk('uploads', storage_path('app/uploads'));
        $diskService->addDisk('documents', storage_path('app/uploads/documents'));
        
        // Separate disks for PDF, DOC, XLS
        $diskService->addDisk('pdfs', storage_path('app/uploads/pdfs')); // For PDF files
        $diskService->addDisk('docs', storage_path('app/uploads/docs')); // For DOC files
        $diskService->addDisk('xls', storage_path('app/uploads/xls')); // For XLS files
        
        // Video disk
        $diskService->addDisk('video', storage_path('app/uploads/video'));
        
        // Additional disk types
        $diskService->addDisk('audio', storage_path('app/uploads/audio'));
        $diskService->addDisk('archives', storage_path('app/uploads/archives'));
        $diskService->addDisk('spreadsheets', storage_path('app/uploads/spreadsheets'));
        $diskService->addDisk('presentations', storage_path('app/uploads/presentations'));
        $diskService->addDisk('backups', storage_path('app/uploads/backups'));
        $diskService->addDisk('data', storage_path('app/uploads/data'));

        $diskService->addDisk('testing', storage_path('app/uploads/testing'));
    }
}
