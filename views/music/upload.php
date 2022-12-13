<?php

use \app\assets\UploadAssets;
UploadAssets::register($this);

$this->title = "Upload file";

?>

<audio src="" hidden="true" controls id="Sounds"></audio>

<form class="gy-2 gx-3 align-items-center p-3 border rounded">
    <div class="row">
        <div class="col-6">
            <label class="visually-hidden" for="song_name">Song Name : </label>
            <input type="text" class="form-control" id="song_name" placeholder="Author - Title">
        </div>
        <div class="col-4">
            <label class="visually-hidden" for="genres">Preference</label>
            <select class="form-select" id="genres">
                <?php $Iterator = 0; ?>
                <?php foreach($AllGenres as $val): ?>
                    <option value="<?=$val->id?>" <?=($Iterator == 0 ? "selected" : "")?>><?=$val->name?></option>
                    
                    <?php $Iterator += 1; ?>
                <?php endforeach;?>
            </select>
        </div>
        <div class="col-auto">
            <button id="ButtonUpload" type="button" class="disabled btn btn-primary">Загрузить</button>
        </div>
    </div>
    <label class="FileLoad" ondragover="event.preventDefault();" for="FileUpload">
        <input type="file" ondragover="event.preventDefault();" accept="audio/mp3, audio/wav, audio/ogg" id="FileUpload">
    </label>
</form>

<div id="Output">
    
</div>