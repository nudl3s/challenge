<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->css('bootstrap.min') ?>
    <?= $this->Html->css('custom') ?>
    <?= $this->Html->script('jquery-3.4.1.min') ?>
    <?= $this->Html->script('jquery.form.min', ['block' => 'scriptBottom']) ?>
    <?= $this->Html->script('bootstrap.min', ['block' => 'scriptBottom']) ?>
    <?= $this->Html->script('popper.min', ['block' => 'scriptBottom']) ?>
    <?= $this->Html->script('custom', ['block' => 'scriptBottom']) ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>

</head>
<body>
    <!-- Nav Bar -->


    <nav class="navbar navbar-expand-lg navbar-dark custom-nav">
        <div class="container">
            <a class="navbar-brand" href="#">Challenge</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>


            <div class="collapse navbar-collapse" id="navbarSupportedContent">

                <?php
                    $action = !empty($this->request->getParam('action')) ? $this->request->getParam('action') : '';
                ?>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item <?= ($action == 'home' ? 'active' : ''); ?>">
                        <?php echo $this->Html->link( 'Home', '/', ['class' => 'nav-link'] ); ?>
                    </li>
                    <li class="nav-item <?= ($action == 'upload' ? 'active' : ''); ?>">
                        <?php echo $this->Html->link( 'Upload', '/upload', ['class' => 'nav-link'] ); ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?= $this->Flash->render() ?>
    <div class="container clearfix">
        <?= $this->fetch('content') ?>
    </div>
    <footer>
    </footer>
    <?= $this->fetch('scriptBottom') ?>
</body>
</html>
