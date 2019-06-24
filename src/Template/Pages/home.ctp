<div class="container p-3">
    <table class="table table-flush">
        <thead class="thead-light">
            <th>Video Name</th>
            <th>Format</th>
            <th>Size</th>
        </thead>
        <tbody>
        <?php foreach($videoFiles as $videoFile) { ?>
            <tr>
                <td>
                    <a class="nav-link" href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'single', $videoFile->id]); ?>">
                        <?= $videoFile->name; ?>
                    </a>

                </td>
                <td><?= $videoFile->extension; ?></td>
                <td>
                    <?php
                        $units = array('B', 'KB', 'MB', 'GB', 'TB');

                        $bytes = max($videoFile->size, 0);
                        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
                        $pow = min($pow, count($units) - 1);

                        $bytes /= pow(1024, $pow);
                        echo round($bytes, '2') . ' ' . $units[$pow];

                    ?>
                    </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
