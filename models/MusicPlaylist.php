<?php

namespace app\models;

use yii\db\ActiveRecord;

class MusicPlaylist extends ActiveRecord
{
    public static function tableName()
    {
        return "music_playlist";
    }

    public static function AddToPlaylist(string $NameSong, string $PathToSong, string $Genre, string $image = null)
    {
        $AppMusic = new MusicPlaylist();
        $AppMusic->songname = $NameSong;
        $AppMusic->songfile = $PathToSong;
        $AppMusic->imagefile = null;
        $AppMusic->genre = $Genre;
        $AppMusic->whoupload = \Yii::$app->user->getId();

        if(!$AppMusic->save())
        {
            return -1;
        }

        return 0;

        // // Song Name and Original not Redacted
        // $NameSong = $this->request->post('name');
        // $NameSong_Orig = trim($this->request->post('name'));

        // // Blob file with mp3 128kbps
        // $Mp3Data = $_FILES['song'];

        // $Genre = $this->request->post('genre');

        // // Filter Name;
        // $NameSong = str_replace(' ', '_', $NameSong);
        // $NameSong = str_replace('%', '_', $NameSong);
        // $NameSong = str_replace('#', '_', $NameSong);
        // $NameSong = str_replace('$', '_', $NameSong);
        // $NameSong = str_replace('&', '_', $NameSong);
        // $NameSong = str_replace('*', '_', $NameSong);
        // $NameSong = str_replace('@', '_', $NameSong);
        // $NameSong = str_replace('!', '_', $NameSong);
        // $NameSong = str_replace('?', '_', $NameSong);

        // if($Mp3Data != null)
        // {
            
        //     $Target_dir = "/uploads/";
        //     $Target_file = $Target_dir . basename($Mp3Data["name"]);

        //     $FileType = strtolower(pathinfo($Target_file,PATHINFO_EXTENSION));

        //     $Target_file = $_SERVER['DOCUMENT_ROOT'] . $Target_dir . basename("$NameSong.$FileType");
            
        //     $Output[static::JSON_CONTENT] = $Target_file;

        //     if (file_exists($Target_file)) {
        //         $Output[static::JSON_HEADER]["Code"] = -1;
        //         $Output[static::JSON_HEADER]["Message"] = "File with this name exists";
        //     }

        //     if (move_uploaded_file($_FILES['song']["tmp_name"], $Target_file))
        //     {
        //         

        //         if($AppMusic->save())
        //         {
        //             $Output[static::JSON_HEADER]["Code"] = 0;
        //             $Output[static::JSON_HEADER]["Message"] = "Uploaded!";
        //         }
        //         else
        //         {
        //             unlink($Target_file);
        //         }                        
        //     }

        //     return json_encode($Output);
        // }
        // else
        // {
        //     $Output[static::JSON_HEADER]["Code"] = -1;
        //     $Output[static::JSON_HEADER]["Message"] = "File is empty!";
        // }
    }

    public static function CountPagesFromDataPlaylist($GenreId, $Limit)
    {
        return ceil( MusicPlaylist::find() -> where( ($GenreId != 0 ? "genre = $GenreId" : '') ) -> count()
            * (1 / $Limit)
        );
    }

    public static function GetFromPlaylist($GenreId = 0, $OffsetIndex = 0, $Limit = 5)
    {
        /*
        SELECT 
            music_playlist.songname as name, 
            music_playlist.songfile as track, 
            genres.name, 
            music_playlist.imagefile as image 
        FROM music_playlist 
        LEFT JOIN 
            genres 
                ON music_playlist.genre = genres.id
        LEFT JOIN 
            user 
                ON music_playlist.whoupload = user.id
        LIMIT 5 
        OFFSET 0
        */

        $DataTo = (new \yii\db\Query())->select(
            [
                'music_playlist.songname AS name',
                'music_playlist.songfile as track',
                'genres.name as genre',
                'music_playlist.imagefile as image',
                'user.username as who',
            ]
        )->from(
            'music_playlist'
        )->leftJoin(
            'genres',
            'music_playlist.genre = genres.id'
        )->leftJoin(
            'user',
            'music_playlist.whoupload = user.id'
        )->where(
            ($GenreId != 0 ? "music_playlist.genre = $GenreId" : '')
        )->orderBy(
            'music_playlist.id DESC'
        )->limit(
            $Limit
        )->offset(
            $OffsetIndex * $Limit
        )->all();

        return json_encode($DataTo);
    }
}

?>