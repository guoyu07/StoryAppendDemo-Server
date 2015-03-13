<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <?php
    $this->beginContent('//layouts/header');
    $this->endContent();
    ?>
</head>
<body>
    <?php
    $this->beginContent('//layouts/menu');
    $this->endContent();
    ?>
    <div class="root-container">
        <?= $content; ?>
    </div>
</body>
</html>
