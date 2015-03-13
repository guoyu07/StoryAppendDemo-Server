<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 5/5/14
 * Time: 3:31 PM
 */
class FileUtility
{
    public static function uploadToQiniu($file, $absolute_path = false)
    {
        if (Yii::app()->params['DEBUG']) {
            return '';
        }
        include_once('ToQiniu.php');

        if (!$absolute_path) {
            $file = Yii::app()->params['DIR_UPLOAD_ROOT'] . $file;
        }

        return upload_to_qiniu($file);
    }

    public static function uploadFile($to_dir, $fileTypes = array('jpg', 'jpeg', 'png'), $fileTypeError = '请上传以下类型图片：JPG, PNG.', $absolute_to_dir = false, $keep_file_name = false)
    {
        $src_filename = html_entity_decode($_FILES['file']['name'], ENT_QUOTES, 'UTF-8');

        if (!in_array(strtolower(substr(strrchr($src_filename, '.'), 1)), $fileTypes)) {
            return array('code' => 400, 'msg' => $fileTypeError);
        }

        if ($_FILES['file']['error'] != UPLOAD_ERR_OK) {
            $error_msg = '未知原因。';
            switch ($_FILES["file"]['error']) {
                case 1:
                    $error_msg = '警告： 上传的文件超过了在php.ini配置中的上传文件大小上限！';
                    break;
                case 2:
                    $error_msg = '警告： 上传的文件超过了在HTML表单内指定的上传文件大小上限！';
                    break;
                case 3:
                    $error_msg = '警告： 只上传了部份文件！';
                    break;
                case 4:
                    $error_msg = '警告： 没有上传文件！';
                    break;
                case 6:
                    $error_msg = '警告： 缺少临时文件夹！';
                    break;
                case 7:
                    $error_msg = '警告： 无法写入文件！';
                    break;
                case 8:
                    $error_msg = '警告： 文件上传终止！';
                    break;
                case 999:
                    $error_msg = '警告： 没有可提供的错误代码！';
                    break;
            }

            return array('code' => 400, 'msg' => '上传失败：' . $error_msg);
        }

        if (is_uploaded_file($_FILES['file']['tmp_name']) && file_exists($_FILES['file']['tmp_name'])) {
            $file = basename($src_filename);
            if (!$keep_file_name) {
                $file = date('Ymd_His', time()) . '_' . $file;
            }

            $path = $to_dir;
            if (!$absolute_to_dir) {
                $path = Yii::app()->params['DIR_UPLOAD_ROOT'] . $path;
            }

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            move_uploaded_file($_FILES['file']['tmp_name'], $path . $file);

            return array('code' => '200', 'msg' => '上传成功！', 'file' => $file);
        }

        return array('code' => '400', 'msg' => '上传失败！');
    }

    public static function downloadToFile($url)
    {
        $result = HTTPRequest::request($url);
        if ($result['Status'] == 'OK') {
            $path = Yii::app()->params['DIR_UPLOAD_ROOT'] . 'data/temp/';
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            $filename = $path . md5($url) . '.png';
            file_put_contents($filename, $result['content']);

            return $filename;
        }

        return '';
    }

    public static function deleteFile($file)
    {
        if (file_exists($file) && is_file($file)) {
            unlink($file);
        }
    }

    public static function deleteDir($dir)
    {
        //先删除目录下的文件：
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    deldir($fullpath);
                }
            }
        }
        closedir($dh);
        //删除当前文件夹：
        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }

    public static function zipFilesWithFilter($zip_file, $path, $filter = ".pdf", $to_windows = false)
    {
        $zip = new ZipArchive();
        $opened = $zip->open($zip_file, ZIPARCHIVE::OVERWRITE);
        if ($opened !== true) {
            return false;
        }

        $count = 0;
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }
                    $could_add = true;
                    if (!empty($filter)) {
                        $could_add = false;
                        if (is_array($filter)) {
                            foreach ($filter as $ext) {
                                if (strpos($file, $ext)) {
                                    $could_add = true;
                                    break;
                                }
                            }
                        } else {
                            if (strpos($file, $filter)) {
                                $could_add = true;
                            }
                        }
                    }

                    if ($could_add) {
                        $addfilename = $file;
                        if ($to_windows) {
                            $addfilename = iconv('UTF-8', 'GBK', $file);
                        }
                        $zip->addFile($path . $file, $addfilename);
                        $count++;
                    }
                }
            }
        }
        if (!$zip->close()) {
            return false;
        }

        return $count > 0;
    }

    public static function zipFiles($files_dir, $destination_file)
    {
        if (!file_exists($files_dir) || file_exists($destination_file)) {
            return false;
        }

        $pathInfo = pathInfo($files_dir);
        $parentPath = $pathInfo['dirname'];
        $dirName = $pathInfo['basename'];

        $zip = new ZipArchive();
        $zip->open($destination_file, ZIPARCHIVE::CREATE);
        $zip->addEmptyDir($dirName);
        self :: folderToZip($files_dir, $zip, strlen("$parentPath/"));
        if ($zip->close()) {
            return true;
        } else {
            return false;
        }
    }

    private static function folderToZip($folder, &$zipFile, $exclusiveLength)
    {
        $handle = opendir($folder);
        while (false !== $f = readdir($handle)) {
            if ($f != '.' && $f != '..') {
                $filePath = "$folder/$f";
                // Remove prefix from file path before add to zip.
                $localPath = substr($filePath, $exclusiveLength);
                if (is_file($filePath)) {
                    $zipFile->addFile($filePath, $localPath);
                } elseif (is_dir($filePath)) {
                    // Add sub-directory.
                    $zipFile->addEmptyDir($localPath);
                    self::folderToZip($filePath, $zipFile, $exclusiveLength);
                }
            }
        }
        closedir($handle);
    }

    public static function collectFiles($root, $ext = 'jpg')
    {
        $result = array();

        $files = array_diff(scandir($root), array('.', '..'));
        foreach ($files as $file) {
            if (!(strpos($file, 'MACOSX') === false)) {
                continue;
            }
            if (is_dir("$root$file")) {
                // just ignore it
            } else {
                $file_ext = strtolower(substr($file, -3, 3));
                if (is_array($ext)) {
                    if (in_array($file_ext, $ext)) {
                        array_push($result, $file);
                    }
                } else {
                    if ($file_ext === $ext) {
                        array_push($result, $file);
                    }
                }

            }
        }

        return $result;
    }

    public static function loadClassWithoutYii($lib_class)
    {
        spl_autoload_unregister(array('YiiBase', 'autoload')); //反注册Yii框架的autoload方法（与swift的autoload有冲突）
        require_once $lib_class;
        spl_autoload_register(array('YiiBase', 'autoload')); //恢复Yii框架autoload方法
    }

    public static function render($viewFile, $data)
    {
        extract($data);
        ob_start();
        ob_implicit_flush(false);
        require($viewFile);
        $output = ob_get_contents();
        ob_get_clean();

        return $output;
    }

    public static function replaceInvalidChars($file_name)
    {
        $invalid_chars = array(' ', '`', '~', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '=', '+', '[', ']', '{', '}', '\\', '¦', ':', ';', '"', '\'', ',', '<', '>', '/', '?');
        $corrected_chars = array('_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_');

        $newname = str_replace($invalid_chars, $corrected_chars, $file_name);

        return $newname;
    }

}