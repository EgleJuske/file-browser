<?php
$pathArray = explode('\\', getcwd());
$lastPathElement = end($pathArray);

// ****lOGOUT***** 
session_start();
if (isset($_GET['action']) and $_GET['action'] == 'logout') {
    session_start();
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    unset($_SESSION['logged_in']);
    $_SESSION['logout_msg'] = '<div style="color:orange">Successfully logged out</div>';
    header('Location: /' . $lastPathElement . '/');
    exit;
}

// ****LOGIN***
if (isset($_POST['login']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    if ($_POST['username'] == 'Gurgutis' && $_POST['password'] == '1234') {
        $_SESSION['logged_in'] = true;
        $_SESSION['timeout'] = time();
        $_SESSION['username'] = 'Gurgutis';
        header('Location: /' . $lastPathElement . '/');
    } else {
        print('<div style="color:red">Wrong username or password</div>');
    }
}

// ***NEW DIR***
if (isset($_POST['new-dir-name'])) {
    if (is_dir($_POST['new-dir-name'])) {
        print('<div style="color: red;">The directory with name <i><b>' . $_POST['new-dir-name'] . '</b></i> already exists</div>');
    } else {
        mkdir($_GET['path'] . $_POST['new-dir-name']);
    }
}

// ***DOWNLOAD***
if (isset($_POST['download'])) {
    $file = $_GET['path'] . $_POST['download'];
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

// ***UPLOAD***
if (isset($_FILES['file'])) {
    if ($_FILES['file']['name'] === "") {
        print('<div style="color: red;">No file selected to upload</div>');
    } else {
        $file_name = $_FILES['file']['name'];
        $file_size = $_FILES['file']['size'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_type = $_FILES['file']['type'];
        move_uploaded_file($file_tmp, $_GET['path'] . $file_name);
    }
}
// ***DELETE***
if (isset($_POST['delete'])) {
    unlink($_GET['path'] . $_POST['delete']);
    header('Location: ' . $_SERVER['REQUEST_URI']);
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
    <?php
    if ($_SESSION['logged_in'] === true) {
    ?>
        <h1>This is a very nice file browser</h1>

        <div>You are currently in this category:
            <?php
            print('<span style="font-weight: bold">' . $_SERVER['REQUEST_URI'] . '</span>');
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
                $path = './' . $_GET['path'];
                print($_SERVER['REQUEST_URI']);
                print('<br>');
                print($path);
                $dirResults = scandir($path);
                foreach ($dirResults as $dirResult) {
                    if ($dirResult === '.' || $dirResult === '..') continue;
                    if (is_dir($path . $dirResult)) {
                        $type = 'Directory';
                        if (isset($_GET['path'])) {
                            $name = '<a href="' . $_SERVER['REQUEST_URI'] . $dirResult . '/">' . $dirResult . '</a>';
                        } else {
                            $name = '<a href="' . $_SERVER['REQUEST_URI'] . '?path=' . $dirResult . '/">' . $dirResult . '</a>';
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
        <?php
        if (!isset($_GET['path'])) {
        ?>
            <div class="back-btn inactive">
                <a href="" onclick="return false">Back</a>
            </div>
        <?php
        } else { ?>
            <div class="back-btn active">
                <a href="<?php print(dirname($_SERVER['REQUEST_URI'], 1)); ?>">Back</a>
            </div>
        <?php } ?>

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

        <div>
            Click here to <a href="index.php?action=logout"> logout. </a>
        </div>

    <?php } else { ?>
        <div class="login-form-placeholder">
            <form action="" method="post">
                <input type="text" name="username" placeholder="username = Gurgutis" required autofocus></br>
                <input type="password" name="password" placeholder="password = 1234" required>
                <button class="login-btn" type="submit" name="login">Login</button>
            </form>
        </div>
    <?php } ?>
</body>

</html>