<?php
namespace Html;

function header ($title, $root_url) {
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title><?= $title ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/themes/material_blue.css">
  <link rel="stylesheet" href="<?= $root_url ?>css/styles.css">
</head>
<body>
<header>
  <nav class="my-navbar">
    <a class="my-navbar-brand" href="<?= $root_url ?>">AI Wordsalad</a>
  </nav>
</header>
<main>
<?php
}

function footer () {
?>
</main>
<!-- scripts -->
</body>
</html>
<?php
}


