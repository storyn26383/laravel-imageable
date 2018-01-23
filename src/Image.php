<?php

namespace Sasaya\LaravelImageable;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class Image extends Model
{
    protected $fillable = ['name', 'mime', 'size', 'path'];

    protected $appends = ['url'];

    public static function boot()
    {
        parent::boot();

        static::deleted(function ($image) {
            Storage::delete($image->path);
        });
    }

    public function imageable()
    {
        return $this->morphTo();
    }

    public static function upload(UploadedFile $file, Model $model)
    {
        Storage::putFileAs('images', $file, $filename = uniqid());

        $image = new static([
            'name' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'path' => "images/{$filename}",
        ]);

        $image->imageable()->associate($model);

        $image->save();

        return $image;
    }

    public function response()
    {
        $response = Response::make(Storage::get($this->path));

        $response->header('Content-Type', $this->mime);
        $response->header('Content-Length', $this->size);

        return $response;
    }

    public function getUrlAttribute()
    {
        return URL::route('images.show', $this->id);
    }
}
