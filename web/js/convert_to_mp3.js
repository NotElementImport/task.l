document.body.addEventListener('keydown', (e) => {
    if(e.ctrlKey && e.key == 'l')
    {
        e.preventDefault();
        console.log(FormElements.file_data);
    }
});

const Modal = {
    modal : new bootstrap.Modal('#modal', { keyboard: false }),

    modal_header : document.getElementById('modal-header'),

    modal_text : document.getElementById('modal-text'),

    show_with_text : (header = "Test Header", text = "Test Text") =>{
        Modal.modal_header.innerHTML = header;
        Modal.modal_text.innerHTML = text;

        Modal.modal.show();
    },

    hide : () => {
        Modal.modal.hide();
    }
};

const FormElements = {
    csrf : {
        param_name :  document.querySelector('meta[name=csrf-param]').getAttribute("content"),
        param_value : document.querySelector('meta[name=csrf-token]').getAttribute("content")
    },

    button : document.getElementById('ButtonUpload'),
    button_active : (active = false) => {
        let IsDisabled = FormElements.button.classList.contains('disabled');

        if(active)
        {
            (IsDisabled ? FormElements.button.classList.remove('disabled') : null);
        }
        else
        {
            (!IsDisabled ? FormElements.button.classList.add('disabled') : null);
        }
        
    },

    song_name : document.getElementById('song_name'),
    song_name_value : () => FormElements.song_name.value,

    genres_id : document.getElementById('genres'),
    genres_id_value : () => FormElements.genres_id.options[FormElements.genres_id.selectedIndex].value,

    file : document.getElementById('FileUpload'),
    file_value : () => FormElements.file.files[0],
    file_data : null,

    output_task : document.getElementById('Output'),
    output_task_update : (NewInner) => { FormElements.output_task.innerHTML = NewInner; },

    prepare_to_submit : (type = 'upload') => {
        let Form = new FormData();
        Form.append('method', type);

        Form.append(FormElements.csrf.param_name, FormElements.csrf.param_value);

        switch(type)
        {
            case 'upload':
                Form.append('name', FormElements.song_name_value());
                Form.append('song', FormElements.file_data);
                Form.append('genre', FormElements.genres_id_value());
                break;
        }

        return Form;
    }
};

const Validate = () => {
    if(FormElements.song_name_value().length < 6)
    {
        Modal.show_with_text('Ошибка валидации!', 'Имя трека, должно быть больше 6 символов!');
        return false;
    }

    if(FormElements.file_data == null)
    {
        Modal.show_with_text('Ошибка валидации!', 'Вы должны загрузить звуковой файл!');
        return false;
    }

    return true;
};

const FileValidate = () => {
    let Feedback = true;

    let ValidateFormats = ['wav','mp3','ogg'];

    let Mime = FormElements.file_data.name.split('.');
    Mime = Mime[Mime.length - 1];

    if(ValidateFormats.includes(Mime) == false)
    {
        Modal.show_with_text('Ошибка валидации!', 'Данный формат файла не поддерживаеться!');
        Feedback = false;
    }

    if(FormElements.file_data.size > 15169664)
    {
        Modal.show_with_text('Ошибка валидации!', 'Файл весит больше 15mb!');
        Feedback = false;
    }

    FormElements.button_active(Feedback);
};

const PrepareToWork = () => {
    console.log('prepare');

    FormElements.file.parentNode.addEventListener('drop', (ev) => {
        ev.preventDefault();

        FormElements.file_data = ev.dataTransfer.items[0].getAsFile();

        FormElements.button_active(true);

        FileValidate();
    });

    FormElements.file.addEventListener('dragover', (ev) => {
        ev.preventDefault();
        console.log('feel');
    });

    FormElements.file.addEventListener('dragenter', (ev) => {
        ev.preventDefault();
        console.log('feel');
    });

    FormElements.file.addEventListener('change', (ev) => {
        ev.preventDefault();

        FormElements.file_data = FormElements.file_value();

        FormElements.button_active(true);

        FileValidate();
    });

    FormElements.button.addEventListener('click', (ev) => {
        UploadForm();
    });

};

setTimeout(PrepareToWork, 200);

const UploadForm = () => {
    if(Validate())
    {
        fetch('', 
            {
                method : 'post',
                body : FormElements.prepare_to_submit('upload')
            }
        ).then(e => e.text()).then(e => {
            if(e.startsWith('!'))
            {
                Modal.show_with_text('Ошибка!', e);
            }
            else
            {
                Modal.show_with_text('', e);

                GetAllDataFromPoll();
            }
        });
    }
};

const GetAllDataFromPoll = () => {
    fetch('', 
        {
            method : 'post',
            body : FormElements.prepare_to_submit('poll')
        }
    ).then(e => e.json()).then(e => {
        let Result = "";

        console.log(e);

        for(let i = 0; i < e.length; i++)
        {
            let Inner = e[i];

            const TemplateAllPullData = `
            <div style='margin-top: 1em;'>
                <div class='border rounded row'>
                    <div class='col-md-10 text-md-start p-2'>
                        Название : ${Inner.name} / Жанр : ${Inner.genre}
                    </div>
                    <div class='col p-2  ${Inner.done ? 'bg-primary' : 'bg-warning'} fw-bold'>
                       ${Inner.done ? 'Готово' : 'В обработке'}
                    </div>
                </div>
            </div>
            `;

            Result += TemplateAllPullData;
        }

        FormElements.output_task_update(Result);
    });
};

GetAllDataFromPoll();