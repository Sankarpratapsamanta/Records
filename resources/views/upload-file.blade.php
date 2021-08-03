<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>{{ config('app.name') }}</title>
</head>

<style>
    .card-footer, .progress {
        display: none;
    }
</style>

<body>
<div class="container pt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <h5>Upload File</h5>
                </div>

                <div class="card-body">
                    <div id="upload-container" class="text-center">
                        <button id="browseFile" class="btn btn-primary">Brows File</button>
                    </div>
                    <button type="button" class="btn btn-warning" aria-label="Pause upload" id="pause-upload-btn">
                        <span class="glyphicon glyphicon-pause " aria-hidden="true"></span> Pause
                    </button>
                    <button id="cancel-button" class="btn btn-danger">Cancel</button>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <!-- <div class="card-footer p-4" >
                    <video id="videoPreview" src="" controls style="width: 100%; height: auto"></video>
                </div> -->
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<!-- <script src="{{ asset('assets/js/jQuery.min.js') }}" ></script> -->
<!-- Bootstrap JS Bundle with Popper -->
<script src="{{ asset('assets/js/bootstrap5-bundle.min.js') }}" ></script>
<!-- Resumable JS -->
<script src="https://cdn.jsdelivr.net/npm/resumablejs@1.1.0/resumable.min.js"></script>

<script type="text/javascript">
    let browseFile = $('#browseFile');
    let resumable = new Resumable({
        target: '/upload',
        query:{_token:'{{ csrf_token() }}'} ,// CSRF token
        fileType: ['mp4','csv','zip'],
        headers: {
            'Accept' : 'application/json'
        },
        testChunks: false,
        throttleProgressCallbacks: 1,
    });
    // if(!resumable.support) location.href = '/upload';
    resumable.assignBrowse(browseFile[0]);

    resumable.on('fileAdded', function (file) { // trigger when file picked
    // console.log('picked')
        showProgress();
        resumable.upload() // to actually start uploading.
    });

    resumable.on('fileProgress', function (file) { // trigger when file progress update
    // console.log('process')
        updateProgress(Math.floor(file.progress() * 100));
    });

    resumable.on('fileSuccess', function (file, response) { // trigger when file upload complete
        // response = JSON.parse(response)
        $('#videoPreview').attr('src', response.path);
        // $('.card-footer').show();
        alert('file uploaded successfully ! ')

    });

    // resumable.on('progress', function(file){
    //     updateProgress(Math.floor(resumable.progress() * 100));
    //     $('#pause-upload-btn').find('.glyphicon').removeClass('glyphicon-play').addClass('glyphicon-pause');
    // });

    $('#pause-upload-btn').click(function(){
        if (resumable.files.length>0) {
            if (resumable.isUploading()) {
            $(this).text("Resume");
              return  resumable.pause();
            }
            $(this).text("Pause");
            return resumable.upload();
        }
    });
    resumable.on('pause',function(){
        $('#pause-upload-btn').find('.glyphicon').removeClass('glyphicon-pause').addClass('glyphicon-play');
    })


    $('#cancel-button').click(function(){
        // chunk.abort();
        resumable.cancel();
        $.get("{{url('/delete')}}", function(data, status){
            console.log("Data: " + data + "\nStatus: " + status);
        });
        hideProgress();
    })
    // resumable.on('cancel',function(){
    // })
    resumable.on('fileError', function (file, response) { // trigger when there is any error
    // console.log(file);
    // console.log(response);
        alert('file uploading error.')
    });


    let progress = $('.progress');
    function showProgress() {
        progress.find('.progress-bar').css('width', '0%');
        progress.find('.progress-bar').html('0%');
        progress.find('.progress-bar').removeClass('bg-success');
        progress.show();
    }

    function updateProgress(value) {
        progress.find('.progress-bar').css('width', `${value}%`)
        progress.find('.progress-bar').html(`${value}%`)
    }

    function hideProgress() {
        progress.hide();
    }
</script>
</body>
</html>
