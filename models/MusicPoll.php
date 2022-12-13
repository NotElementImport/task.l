<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class MusicPoll extends ActiveRecord
{
    public static function tableName()
    {
        return "music_poll";
    }

    public static function RegisterToPoll(string $name, $file, string $genre)
    {
        $FileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $FilePath = $_SERVER['DOCUMENT_ROOT'].'/uploads/tmp_ffmpeg_'.$name.'.'.$FileType;
        $FilePath_orig = $_SERVER['DOCUMENT_ROOT'].'/uploads/'.$name.'.'.$FileType;

        if(file_exists($FilePath))
        {
            return -2;
        }
        elseif(file_exists($FilePath_orig))
        {
            return -2;
        }
        else
        {
            move_uploaded_file($file['tmp_name'], $FilePath);
        }

        $id = Yii::$app->queue->push(new \app\jobs\UploadJob([
            'name' => $name,
            'file' => '/uploads/tmp_ffmpeg_'.$name.'.'.$FileType,
            'genre' => $genre
        ]));

        $MusicPollNew = new MusicPoll();
        $MusicPollNew->user_id = Yii::$app->user->getId();
        $MusicPollNew->queue_id = $id;
        $MusicPollNew->name = $name;
        $MusicPollNew->genre = $genre;

        if($MusicPollNew->save())
        {
            return 0; 
        }

        return -1;
    }

    public static function GetFromPoll($Limit = 5)
    {
        // SELECT 
        //     IF(queue.done_at IS NULL, FALSE, TRUE) as 'Done', 
        //     music_poll.name, 
        //     genres.name, 
        //     music_poll.queue_id  
        // FROM `music_poll` 
        // LEFT JOIN
        //     queue
        //     ON music_poll.queue_id = queue.id
        // LEFT JOIN
        //     genres
        //     ON music_poll.genre = genres.id
        // WHERE music_poll.user_id = 2

        $UserId = Yii::$app->user->getId();

        $Answer = (new Query()
            )->select([
                'IF(queue.done_at IS NULL, FALSE, TRUE) as done',
                'music_poll.name',
                'genres.name as genre',
                'music_poll.queue_id'
            ]
            )->from(
                'music_poll'
            )->leftJoin(
                'queue',
                'music_poll.queue_id = queue.id'
            )->leftJoin(
                'genres',
                'music_poll.genre = genres.id'
            )->where(
                'music_poll.user_id = '.$UserId
            )->limit(
                $Limit
            )->orderBy(
                'music_poll.queue_id DESC'
            )->all();

        return $Answer;
    }
}

?>