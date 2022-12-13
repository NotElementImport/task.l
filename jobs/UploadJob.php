<?php
namespace app\jobs;

use app\models\MusicPlaylist;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use FFMpeg\FFMpeg;

class UploadJob extends BaseObject implements JobInterface
{
    public $name;
    public $file;
    public $genre;

    const FOLDER_CONTENT = '/uploads/';

    public function execute($queue)
    {
        printf($_SERVER['DOCUMENT_ROOT']);
        printf(__DIR__);

        $CompressMP3 = FFMpeg::create(array(
            'ffmpeg.binaries'  => __DIR__.'/../bin/ffmpeg.exe', // the path to the FFMpeg binary
            'ffprobe.binaries' => __DIR__.'/../bin/ffprobe.exe', // the path to the FFProbe binary
            'timeout'          => 3600, // the timeout for the underlying process
            'ffmpeg.threads'   => 12,   // the number of threads that FFMpeg should use
        ));

        $Audio = $CompressMP3->open('/../web'.$this->file);
        $Format = new \FFMpeg\Format\Audio\Mp3();
        $Format->setAudioKiloBitrate(256);

        $Path = $this->name.'.mp3';

        $Audio->save($Format, $Path);

        MusicPlaylist::AddToPlaylist($this->name, $Path, $this->genre);
    }
}