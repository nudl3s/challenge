<div class="container p-5">
    <div class="row no-gutters">
        <div class="col-sm-6 offset-sm-3">
            <h3>Here you can upload your video files</h3>

            <?= $this->Form->create($file, ['type' => 'file', 'id' => 'videoForm']); ?>
            <div class="form-group inputDnD">
                <label class="sr-only" for="inputFile">File Upload</label>
                <?= $this->Form->input($file, ['type' => 'file', 'name' => 'file', 'id' => 'file', 'class' => 'form-control-file text-success font-weight-bold', 'data-title' => 'Drag and drop a file']); ?>
            </div>

            <div class="progress m-3">
                <div class="progress-bar progress-bar-striped progress-bar-success active" role="progressbar"
                     aria-valuenow="0" aria-valuemin="0"
                     aria-valuemax="100" style="width: 0%;"><span>0%</span></div>
            </div>
            <div class="form-group">
                <?= $this->Form->button(__('Upload'), ['type' => 'submit', 'class' => 'form-control btn btn-default my-2 uploadBtn']); ?>
            </div>

            <?= $this->Form->end(); ?>
        </div>
    </div>

    <div class="row no-gutters m-5" id="uploadedFiles">
        <h4>Uploaded Files:</h4>
    </div>
</div>


<script type="text/javascript">
    // $("#videoForm").on("submit", function(e){
    //     e.preventDefault();
    //     if ($('#file').val())
    //     {
    //         if (!isVideo($('#file').val())) {
    //             return alert('Please select a valid video file.');
    //         }
    //         $('.progress').show();
    //         var formData = new FormData($('#videoForm')[0]);
    //         $.ajax({
    //             type: "POST",
    //             url: '<?= $this->Url->build(["controller" => "Pages", "action" => "upload"]); ?>',
    //             data: formData,
    //             contentType: false,
    //             processData: false,
    //             xhr: function() {
    //                 var xhr = new window.XMLHttpRequest();
    //                 xhr.upload.addEventListener("progress", function(evt) {
    //                     if (evt.lengthComputable) {
    //                         var percentComplete = (evt.loaded / evt.total) * 100;
    //                         $('.progress-bar').css("width", percentComplete + "%");
    //                         //Do something with upload progress here
    //                     }
    //                 }, false);
    //
    //                 return xhr;
    //             },
    //             success:function(result){
    //                 var obj = JSON.parse(result);
    //
    //                 var resultVideo = '<div class="col-md-12 my-2"><a href="' + obj.url +'">' + obj.name + '</a></div>';
    //                 $('#uploadedFiles').append(resultVideo);
    //                 $('.progress').hide();
    //                 $('.progress-bar').css("width", "0%");
    //                 $('#file').val('');
    //             }
    //         });
    //     }
    //     return false;
    // });

    function isVideo(filename) {
        var ext = getExtension(filename);
        switch (ext.toLowerCase()) {
            case 'm4v':
            case 'avi':
            case 'mpg':
            case 'mp4':
            case 'mkv':

                return true;
        }
        return false;
    }

    function getExtension(filename) {
        var parts = filename.split('.');
        return parts[parts.length - 1];
    }

    $("html").on("dragover", function(e) { e.preventDefault(); e.stopPropagation(); });

    $("html").on("drop", function(e) { e.preventDefault(); e.stopPropagation(); });

    // Drop
    $('.inputDnD').on('drop', function (e) {
        e.stopPropagation();
        e.preventDefault();

        if (e.originalEvent.dataTransfer.files.length > 1) {
            return alert('You can add only one file.');
        } else {
            var files = e.originalEvent.dataTransfer.files;
            if (files) {
                $('input[type=file]').prop('files', e.originalEvent.dataTransfer.files);
                $('#file').attr('data-title', files[0].name);
            }
        }

    });

    $('#file').on("change", function(){
        var filename = $(this).val().replace(/.*(\/|\\)/, '');
        $('#file').attr('data-title', filename);
    });


    $("#videoForm").on("submit", function(e) {
        e.preventDefault();

        if ($('#file').val()) {
            if (!isVideo($('#file').val())) {
                return alert('Please select a valid video file.');
            }
            var File = $('#file');
            var formData = new FormData($('#videoForm')[0]), fileMeta = File[0].files[0], fileSize = fileMeta.size,
                bytesUploaded = 0;

            formData.append('file', fileMeta);

            File.attr('disabled', true);
            initiateUpload(formData, fileMeta, function () {
                upload(formData, fileSize, function (data) {
                    bytesUploaded = data;

                    renderProgressBar(bytesUploaded, fileSize);
                }, function (uploadKey) {
                    cleanUp();


                });
            });
        }
    });


    function initiateUpload(formData, fileMeta, cb) {
        $.ajax({
            type: 'POST',
            url: '<?= $this->Url->build(["controller" => "Pages", "action" => "verify"]); ?>',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (response) {
                if ('error' === response.status) {
                    $('#error').html(response.error).fadeIn(200);

                    cleanUp();

                    return;
                }

                renderProgressBar(response.bytes_uploaded, fileMeta.size);

                if ('error' !== response.status) {
                    cb();
                }
            },
            error: function (error) {
                $('#error').fadeIn(200);

                $('#upload').attr('disabled', false).text('Upload');
                $('#tus-file').attr('disabled', true);
            }
        });
    }



    function upload(formData, fileSize, cb, onComplete) {
        $('#upload').text('Uploading...');

        $.ajax({
            type: 'POST',
            url: '<?= $this->Url->build(["controller" => "Pages", "action" => "upload"]); ?>',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (response) {
                if ('error' === response.status) {
                    $('#error').html(response.error).fadeIn(200);

                    cleanUp();

                    return;
                }

                var bytesUploaded = response.bytes_uploaded;

                cb(bytesUploaded);

                if (bytesUploaded < fileSize) {
                    upload(formData, fileSize, cb, onComplete);
                } else {
                    onComplete(response.upload_key);
                }
            },
            error: function (error) {
                $('#error').fadeIn(200);
            }
        });
    }



    var renderProgressBar = function (bytesUploaded, fileSize) {
        var percent = (bytesUploaded / fileSize * 100).toFixed(2);

        $('.progress-bar')
            .attr('style', 'width: ' + percent + '%')
            .attr('aria-valuenow', percent)
            .find('span')
            .html(percent + '%');

        $('.progress').show();

        console.info('Uploaded: ' + percent + '%');
    }

    var cleanUp = function () {
        $('#selected-file').val('');

        $('.progress').hide(100, function () {
            $('.progress-bar')
                .attr('style', 'width: 0%')
                .attr('aria-valuenow', '0');
        });

        $('#upload').attr('disabled', false).text('Upload');
        $('#tus-file').attr('disabled', false);
    };
</script>
