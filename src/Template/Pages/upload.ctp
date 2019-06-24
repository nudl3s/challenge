<div class="container p-5">
    <div class="row no-gutters">
        <div class="col-sm-6 offset-sm-3">
            <h3>Here you can upload your video files</h3>

            <?= $this->Form->create($file, ['type' => 'file', 'id' => 'videoForm']); ?>
            <div class="form-group inputDnD">
                <label class="sr-only" for="inputFile">File Upload</label>
                <?= $this->Form->input($file, ['type' => 'file', 'name' => 'file', 'id' => 'file', 'class' => 'form-control-file text-success font-weight-bold', 'data-title' => 'Drag and drop a file']); ?>
            </div>

            <div class="progress m-3" style="display: none;">
                <div class="progress-bar" role="progressbar"></div>
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
    $("#videoForm").on("submit", function(e){
        e.preventDefault();
        if ($('#file').val())
        {
            if (!isVideo($('#file').val())) {
                return alert('Please select a valid video file.');
            }
            $('.progress').show();
            var formdatas = new FormData($('#videoForm')[0]);
            $.ajax({
                type: "POST",
                url: '<?= $this->Url->build(["controller" => "Pages", "action" => "upload"]); ?>',
                data: formdatas,
                contentType: false,
                processData: false,
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = (evt.loaded / evt.total) * 100;
                            $('.progress-bar').css("width", percentComplete + "%");
                            //Do something with upload progress here
                        }
                    }, false);

                    return xhr;
                },
                success:function(result){
                    var obj = JSON.parse(result);

                    var resultVideo = '<div class="col-md-12 my-2"><a href="' + obj.url +'">' + obj.name + '</a></div>';
                    $('#uploadedFiles').append(resultVideo);
                    $('.progress').hide();
                    $('.progress-bar').css("width", "0%");
                    $('#file').val('');
                }
            });
        }
        return false;
    });

    function isVideo(filename) {
        var ext = getExtension(filename);
        switch (ext.toLowerCase()) {
            case 'm4v':
            case 'avi':
            case 'mpg':
            case 'mp4':

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
</script>
