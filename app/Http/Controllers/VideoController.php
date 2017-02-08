<?php

namespace App\Http\Controllers;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Filters\Video\WatermarkFilter;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
    public function index()
    {
        return view('video.index');
    }

    public function store(Request $request)
    {
        try{
            $archivo = "";

            if(Input::hasFile('video')){
                $archivo = $this->uploadFileProcessor(Input::file('video'), storage_path('app/videos'));

                session(['video_file' => $archivo]);

                return redirect()->route('video.transform');
            }

        }catch (\Exception $e)
        {
            Log::info("***" . $e->getMessage());
            dd('error ' . $e->getMessage());
        }

        return redirect()->route('video.index');
    }

    public function transform()
    {
        $video_url = storage_path('app/videos') . '/' . session('video_file');
        //$video_url = storage_path('app/videos') . '/1-20160720_165546_2058.mp4';
        //$video_url = storage_path('app/videos') . '/2-20160720_164633_4289.mp4';
        //$video_url = storage_path('app/videos') . '/webm.webm';

        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => 'C:/ffmpeg/bin/ffmpeg.exe',
            'ffprobe.binaries' => 'C:/ffmpeg/bin/ffprobe.exe',
            'timeout'          => 0, // The timeout for the underlying process
            'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
        ]);
        $video = $ffmpeg->open($video_url);

        $cadena ="Esta es una frase de pruebas";
        $picture = imagecreate(350,20);
        $colorfondo = imagecolorallocate($picture,255,255,255);
        imagecolortransparent($picture, $colorfondo);
        $colortexto = imagecolorallocate($picture,255,255,255);
        imagestring($picture,12,0,0, $cadena, $colortexto);
        imagepng($picture, 'creada.png');
        imagedestroy($picture);


        $img_url = 'img/favicon.png';
        $img_url2 = 'creada.png';

        /*$wm1 = new WatermarkFilter($img_url, [
            'position' => 'relative',
            'top' => 50,
            'left' => 50
        ], 1);*/

        $wm2 = new WatermarkFilter($img_url2, [
            'position' => 'relative',
            'top' => 100,
            'right' => 300
        ], 1);

        //$video->addFilter($wm1);
        $video->addFilter($wm2);

        /*$video
            ->filters()
            ->watermark($img_url, [
                'position' => 'relative',
                'top' => 50,
                'right' => 50
            ])
            ->watermark($img_url2, [
                'position' => 'relative',
                'top' => 100,
                'right' => 300
            ]);*/

        $format = new X264('libmp3lame', 'libx264');
        $format->setKiloBitrate(2500);

        $video->save($format, storage_path('app/videos') . '/video_edited.mp4');


        $video = $ffmpeg->open(storage_path('app/videos') . '/video_edited.mp4');
        $video->frame(TimeCode::fromSeconds(5))->save(storage_path('app/videos') . '/frame_video.jpg');

        session(['video_file' => 'video_edited.mp4']);

        return redirect()->route('youtube');
    }
}


//C:/ffmpeg/bin/ffmpeg.exe -loop 1 -i photo1.jpg -r 30 -t 3 -acodec libx264 video_output.pm4
//C:/ffmpeg/bin/ffmpeg.exe -i video1.mp4 -i video_output.mpg -filter_complex '[0:0] [0:1] [1:0] [1:1] concat=n=2:v=1:a=1 [v] [a]' -map '[v]' -map '[a]' output.mp4
