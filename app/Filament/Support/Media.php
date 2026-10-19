<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;

/** Campos de subida reutilizables. Sin SVG (riesgo de XSS) y con límites razonables de tamaño. */
class Media
{
    public static function image(string $name, string $directory): FileUpload
    {
        return FileUpload::make($name)
            ->image()
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->maxSize(15 * 1024)
            ->automaticallyResizeImagesToWidth('2560')
            ->automaticallyUpscaleImagesWhenResizing(false)
            ->imagePreviewHeight('160')
            ->openable()
            ->downloadable();
    }

    public static function pdf(string $name, string $directory): FileUpload
    {
        return FileUpload::make($name)
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->acceptedFileTypes(['application/pdf'])
            ->maxSize(30 * 1024)
            ->openable()
            ->downloadable();
    }

    public static function video(string $name, string $directory): FileUpload
    {
        return FileUpload::make($name)
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->acceptedFileTypes(['video/mp4'])
            ->maxSize(100 * 1024)
            ->helperText('MP4 (H.264), máximo 100 MB. Conviene comprimirlo a 1280 px de ancho antes de subirlo.')
            ->openable();
    }
}
