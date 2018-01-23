<?php

namespace Tests\Unit;

use Mockery as m;
use Tests\TestCase;
use Illuminate\Http\Testing\File;
use Sasaya\LaravelImageable\Image;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Schema\Blueprint;
use Sasaya\LaravelImageable\Traits\Imageable;

class ImageableTest extends TestCase
{
    public function testUpload()
    {
        $file = File::image(str_random() . '.jpg');
        $model = $this->getModel();

        Storage::shouldReceive('putFileAs')->with('images', $file, m::type('string'))->once()->andReturn('path');

        $image = Image::upload($file, $model);

        $this->assertTrue(starts_with($image->path, 'images'));
        $this->assertEquals($file->name, $image->name);
        $this->assertEquals($file->getMimeType(), $image->mime);
        $this->assertEquals($file->getSize(), $image->size);
    }

    public function testDelete()
    {
        $image = $this->getImage();

        Storage::shouldReceive('delete')->with($image->path)->once()->andReturn(true);

        $image->delete();

        $this->assertTrue(true);
    }

    public function testResponse()
    {
        $image = $this->getImage();

        Storage::shouldReceive('get')->with($image->path)->once()->andReturn('content');

        Response::shouldReceive('make')->with('content')->once()->andReturnSelf();
        Response::shouldReceive('header')->with('Content-Type', $image->mime)->once()->andReturnSelf();
        Response::shouldReceive('header')->with('Content-Length', $image->size)->once()->andReturnSelf();

        $this->assertEquals(Response::getFacadeRoot(), $image->response());
    }

    public function testUrl()
    {
        $image = $this->getImage();

        URL::shouldReceive('route')->with('images.show', $image->id)->once()->andReturn('foo');

        $this->assertEquals('foo', $image->url);
    }

    public function testToArrayWithUrl()
    {
        $image = $this->getImage();

        URL::shouldReceive('route')->with('images.show', $image->id)->once();

        $this->assertArrayHasKey('url', $image->toArray());
    }

    public function testImageable()
    {
        $image = $this->getImage();
        $model = $this->getModel();

        $image->imageable()->associate($model);

        $image->save();

        $this->assertTrue($image->imageable->is($model));
        $this->assertTrue($model->image->is($image->fresh()));
        $this->assertTrue($model->images()->first()->is($image->fresh()));
    }

    protected function getModel()
    {
        Schema::create($table = str_random(), function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        $model = new class extends Model {
            use Imageable;
        };

        $model->setTable($table)->save();

        return $model;
    }

    protected function getImage()
    {
        return Image::unguarded(function () {
            $model = $this->getModel();

            return Image::create([
                'name' => 'foo.jpg',
                'mime' => 'image/jpg',
                'size' => 1,
                'path' => 'fake/foo.jpg',
                'imageable_id' => $model->id,
                'imageable_type' => get_class($model),
            ]);
        });
    }
}
