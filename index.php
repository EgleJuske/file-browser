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
        print('<span class="current-dir-name">' . $_SERVER['REQUEST_URI'] . '</span>')
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
            $path = $_GET['path'];
            if (isset($path)) {
                $dir = '.' . $path;
            } else {
                $dir = '.';
            }
            $dirResults = array_diff(scandir($dir, $sorting_order = SCANDIR_SORT_NONE), array('..', '.'));
            foreach ($dirResults as $dirResult) {
                if (is_dir($dir . '/' . $dirResult)) {
                    $type = 'Directory';
                    $name = '<a href="?path=' . $path . '/' . $dirResult . '">' . $dirResult . '</a>';
                } else {
                    $type = 'File';
                    $name = $dirResult;
                }
                print('<tr><td>' . $type . '</td>');
                print('<td>' . $name . '</td>');
                print('<td>buttons</td></tr>');
            }
            ?>
        </tbody>
    </table>
</body>

</html>