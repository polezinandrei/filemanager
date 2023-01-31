<?php
require_once 'Manager.php';

$manager = new Manager();
$resultOperation = [];
$postData = json_decode(file_get_contents('php://input'));

if (isset($_FILES['upload']) && isset($_POST['uploadto'])) {
    $uploaddir = $_POST['uploadto'];
    $uploadfile = $uploaddir. '/' .basename($_FILES['upload']['name']);

    if (move_uploaded_file($_FILES['upload']['tmp_name'], $uploadfile)) {
        $resultOperation = ['message' => 'Файл был успешно загружен.'];
    } else {
        $resultOperation = ['message' => 'Файл не был загружен!', 'err' => '1'];
    }
}

if (isset($postData)) {
    if (isset($postData->type)) {
        switch ($postData->type) {
            case 'create':
                if (isset($postData->path) && isset($postData->name)) {
                    $resultOperation = $manager->creadeDir($postData->path. '/' .$postData->name);
                } else {
                    $resultOperation = ['message' => 'Недостаточно данных для создания', 'err' => '1'];
                }
                break;

            case 'delete':
                if (isset($postData->path) && isset($postData->element) && isset($postData->dir)) {
                    $resultOperation = $manager->deleteElement($postData->element, $postData->dir);
                } else {
                    $resultOperation = ['message' => 'Недостаточно данных для удаления', 'err' => '1'];
                }
                break;

            case 'rename':
                if (isset($postData->path) && isset($postData->element) && isset($postData->name)) {
                    $resultOperation = $manager->renameElement($postData->element, $postData->path. '/' .$postData->name);
                } else {
                    $resultOperation = ['message' => 'Недостаточно данных для переименования', 'err' => '1'];
                }
                break;
        }
    }
    echo json_encode([
        'list'   => $manager->getList($postData->path),
        'status' => $resultOperation,
    ]);
} else {
    echo template('view.php', [
        'list'     => $manager->getList($_SERVER['DOCUMENT_ROOT']),
        'current'  => $_SERVER['DOCUMENT_ROOT'],
        'status'  => $resultOperation,
    ]);
}

function template($view, $data) {
    extract($data);
    ob_start();
    require $view;

    return ob_get_clean();
}