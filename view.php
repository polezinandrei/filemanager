<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>FileManager</title>
</head>
<body>

<div class="top-panel">
    <div class="container flex">
        <div>
            <p class="current-dir"><?=$data['current']?></p>
            <button class="create-dir">Создать папку</button>
            <form enctype="multipart/form-data" method="post" action="/index.php">
                <input type="hidden" name="uploadto" value="<?=$_SERVER['DOCUMENT_ROOT']?>">
                <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
                <input type="file" name="upload">
                <input type="submit" value="Загрузить">
            </form>
        </div>
        <p class="status<?echo ($data['status']['err'] == '1') ? ' error' : '';?>"><?=$data['status']['message']?></p>
    </div>
</div>
<div class="container">
    <div class="list">
        <?php foreach ($data['list'] as $file) { ?>
            <div class="line">
                <a class="element <?echo $file['dir'] == 'true' ? 'dir' : 'file'?>" <?echo $file['dir'] == 'true' ? '' : 'download'?> data-dir="<?=$file['dir']?>" data-path="<?=$file['path']?>" href="<?=$file['url']?>"><?=$file['name']?></a>
                <p>
                    <button class="rename <?echo $file['dir'] == 'true' ? 'dir' : 'file'?>" data-dir="<?=$file['dir']?>" data-path="<?=$file['path']?>">Переименовать</button>
                    <button class="remove <?echo $file['dir'] == 'true' ? 'dir' : 'file'?>" data-dir="<?=$file['dir']?>" data-path="<?=$file['path']?>">Удалить</button>
                </p>
            </div>
        <?php } ?>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>