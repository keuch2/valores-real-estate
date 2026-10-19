<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

/** Miniaturas "-sm" (900 px de ancho) de las imágenes subidas al CMS, generadas la primera vez que se piden. */
class ImageVariants
{
    public const WIDTH = 900;

    public static function smallPath(string $path): string
    {
        return preg_replace('/(\.[a-z0-9]+)$/i', '-sm$1', $path);
    }

    /** Devuelve la ruta de la miniatura si existe o se pudo crear; si no, la original. */
    public static function small(string $path): string
    {
        $disk = Storage::disk('public');
        $small = self::smallPath($path);
        if ($small === $path || $disk->exists($small)) {
            return $small === $path ? $path : $small;
        }
        if (! preg_match('/\.(jpe?g|png|webp)$/i', $path) || ! $disk->exists($path)) {
            return $path;
        }
        try {
            $image = (new ImageManager(new Driver))->read($disk->path($path));
            if ($image->width() <= self::WIDTH) {
                return $path;
            }
            $image->scaleDown(width: self::WIDTH);
            $disk->put($small, (string) (preg_match('/\.png$/i', $path) ? $image->toPng() : $image->toJpeg(80)));

            return $small;
        } catch (Throwable $e) {
            Log::warning('No se pudo generar la miniatura', ['path' => $path, 'error' => $e->getMessage()]);

            return $path;
        }
    }
}
