<?php

namespace app\controllers;

use Yii;
use app\models\Genres;
use app\models\LoginForm;
use yii\web\Controller;
use app\models\MusicPlaylist;
use yii\helpers\Url;

class MusicController extends Controller
{
    const LIMIT_TRACKS = 5;

    const METHOD_UPLOAD = 'upload';
    const METHOD_INFO_ABOUT_POLL = 'poll';

    const GET_PAGE = 'page';
    const GET_GENRE = 'genre';

    const JSON_HEADER = 'Header';
    const JSON_CONTENT = 'Message';

    public function actionIndex()
    {
        // FETCH API
        if($this->request->isPost)
        {
            $Page = $this->request->post(static::GET_PAGE);
            $Genre = $this->request->post(static::GET_GENRE);
            
            // Load Track from Database
            return MusicPlaylist::GetFromPlaylist($Genre, $Page - 1, MusicController::LIMIT_TRACKS);
        }
        else if(!$this->request->get(static::GET_PAGE))
        {
            // If Link not hasn't Page index
            return $this->redirect(Url::toRoute(['/', 'page' => '1', 'filter' => '0']));
        }

        $PageCount = MusicPlaylist::CountPagesFromDataPlaylist($this->request->get('filter'), MusicController::LIMIT_TRACKS);

        $AllGenres = Genres::ListOfGenre();

        return $this->render('index', compact('PageCount', 'AllGenres'));
    }

    public function actionAuthorization()
    {
        if(!Yii::$app->user->isGuest)
        {
            Yii::$app->user->logout();

            return $this->goHome();
        }
        else
        {
            $model = new LoginForm();
            if ($model->load(Yii::$app->request->post()) && $model->login()) 
            {
                return $this->goBack();
            }

            $model->password = '';
            return $this->render('auth', [
                'model' => $model
            ]);
        }
    }

    public function actionUpload()
    {
        // If user Guest or not have Permission;
        if(Yii::$app->user->isGuest)
        {
            return $this->goHome();
        }
        else
        {
            if(Yii::$app->user->can('BasicPermission') == false)
            {
                return $this->goHome();
            }
        }

        // FETCH API
        if($this->request->isPost)
        {
            $Method = $this->request->post('method');
            switch($Method)
            {
                case static::METHOD_UPLOAD:

                    $Feedback = \app\models\MusicPoll::RegisterToPoll(
                        $this->request->post('name'),
                        $_FILES['song'],
                        $this->request->post('genre')
                    );

                    return (
                        $Feedback == 0 
                        ? "Ваш файл был отправлен в очередь загрузки!" 
                        : ($Feedback == -1 
                            ? "!Ошибка сервера ваш файл, не был добавлен в очередь!"
                            : "!Ошибка на сервере есть такой файл. Файл не был добавлен в очередь!")
                    );
                
                case static::METHOD_INFO_ABOUT_POLL:

                    return json_encode(
                        \app\models\MusicPoll::GetFromPoll(static::LIMIT_TRACKS)
                    );
            }
        }
        else
        {
            $AllGenres = Genres::ListOfGenre();

            return $this->render('upload', compact('AllGenres'));
        }
    }
}

?>