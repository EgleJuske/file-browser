<?php

if (isset($_POST['new-dir-name'])) {
    if (is_dir($_POST['new-dir-name'])) {
        print('<div style="color: red;">The directory with name <i><b>' . $_POST['new-dir-name'] . '</b></i> already exists</div>');
    } else {
        mkdir($_GET['path'] . $_POST['new-dir-name']);
    }
}

if (isset($_POST['download'])) {
    $file = './' . $_POST['download'];
    $fileToDownloadEscaped = str_replace("&nbsp;", " ", htmlentities($file, null, 'utf-8'));
    ob_clean();
    ob_start();
    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename=' . basename($fileToDownloadEscaped));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($fileToDownloadEscaped));
    ob_end_flush();
    readfile($fileToDownloadEscaped);
    exit;
}

if (isset($_FILES['file'])) {
    $file_name = $_FILES['file']['name'];
    $file_size = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_type = $_FILES['file']['type'];
    move_uploaded_file($file_tmp, $_GET['path'] . $file_name);
    print('<div style="color: green;">File <i><b>' . $file_name . '</b></i> uploaded</div>');
    header('Location: ' . $currentDir);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Very cool file browser</title>
</head>

<body>
    <h1>This is a very cool file browser</h1>

    <div>You are currently in this category:
        <?php
        $currentDir =  $_SERVER['REQUEST_URI'];
        print('<span class="current-dir-name">' . $currentDir . '</span>');
        ?>
    </div>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (isset($_POST['delete'])) {
                unlink($_GET['path'] . $_POST['delete']);
                header('Location: ' . $currentDir);
            }
            $path = './' . $_GET['path'];

            $dirResults = array_diff(scandir($path), array('..', '.'));
            foreach ($dirResults as $dirResult) {
                if (is_dir($path . '/' . $dirResult)) {
                    $type = 'Directory';
                    if (!isset($_GET['path'])) {
                        $name = '<a href="' . $currentDir . '?path=' . $dirResult . '/">' . $dirResult . '</a>';
                    } else {
                        $name = '<a href="' . $currentDir . $dirResult . '/">' . $dirResult . '</a>';
                    }
                    $buttons = '';
                } else {
                    $type = 'File';
                    $name = $dirResult;
                    $buttons = '<form action="" method="POST">
                                    <button type="submit" name="delete" value="' . $dirResult . '">Delete</button>
                                </form>
                                <form action="" method="POST">
                                    <button type="submit" name="download" value="' . $dirResult . '">Download</button>
                                </form>';
                }
                print('<tr><td>' . $type . '</td>');
                print('<td>' . $name . '</td>');
                print('<td>' . $buttons . '</td></tr>');
            }
            ?>
        </tbody>
    </table>
    <div class="back-button-placeholder">
        <button>Back - history</button>
        <button>Back - up folder</button>
    </div>

    <div class="new-dir-placeholder">
        <form action="" method="POST">
            <input type="text" name="new-dir-name" placeholder="New directory name">
            <input type="submit" value="Submit">
        </form>
    </div>

    <div class="upload-download-placeholder">
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="file" name="file">
            <input type="submit" name="upload" value="Upload file">
        </form>
    </div>

    <!-- <div>
        all the variables <br>
        <?php

        print('$currentDir = ' . $currentDir);
        print('<br><br>');
        print('$path= ' . $path);
        print('<br><br>');
        print('$dirResults = ');
        foreach ($dirResults as $dirResult) {
            print($dirResult . ' -- ');
        }
        print('<br><br>');
        print('$_POST[\'delete\']) = ' . $_POST['delete']);
        print('<br><br>');
        var_dump($_POST['delete']);
        print('<br><br>');

        ?>
    </div> -->
</body>

</html>