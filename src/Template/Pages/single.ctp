<div class="container p-4">
    <div class="row no-gutters m-3">
        <h4><?= $video->name; ?></h4>
    </div>

    <div class="row no-gutters">
        <div>
            <video width="700" controls>
                <source src="<?= $video->path; ?>" type="video/mp4">
                <source src="<?= $video->path; ?>" type='video/x-matroska; codecs="theora, vorbis"'>
                <source src="<?= $video->path; ?>" type="video/ogg">
                <source src="<?= $video->path; ?>" type="video/webm">
                <source src="<?= $video->path; ?>" type="video/x-flv">
                Your browser does not support HTML5 video.
            </video>
        </div>
    </div>

</div>
