<?php
/**
 * User: Ron
 * Date: 2019/04/23 下午7:02
 * 图片上传
 */

namespace common\service\common;

use common\service\core\BaseService;

class MyUploadService extends BaseService {

    var $cls_filename = "";           // Name of the upload file.
    var $cls_tmp_filename = "";       // TMP file Name (tmp name by php).
    var $cls_max_filesize = 33554432; // Max file size.
    var $cls_filesize = "";            // Actual file size.
    var $cls_arr_ext_accepted = [".gif", ".jpg", ".jpeg", ".png", ".bmp", ".doc", ".xls", ".pdf", ".txt", ".rar"];
    var $cls_file_rename_to = '';     // New name for the file after upload.
    var $cls_verbal = 0;              // Set to 1 to return an a string instead of an error code.

    function MyUpload($file_name, $tmp_file_name, $file_size, $file_rename_to = '')
    {
        $this->cls_filename = $file_name;
        $this->cls_tmp_filename = $tmp_file_name;
        $this->cls_filesize = $file_size;
        $this->cls_file_rename_to = $file_rename_to;
    }

    function upload($dir)
    {
        if (!in_array(strtolower(strrchr($this->cls_filename, ".")), $this->cls_arr_ext_accepted) && $this->cls_filename != "") {
            exit;
        } elseif ($this->cls_filesize > $this->cls_max_filesize) {
            exit;
        } else {
            $allchar = "abcdefghijklnmopqrstuvwxyz1234567890";
            $this->cls_file_rename_to = date("YmdHi");
            for ($i = 0; $i < 3; $i++) {
                $this->cls_file_rename_to .= substr($allchar, mt_rand(0, 25), 1);
            }
            $this->cls_file_rename_to .= "." . $this->getSuffix($this->cls_filename);
            move_uploaded_file($this->cls_tmp_filename, $dir . $this->cls_file_rename_to);
            return $this->cls_file_rename_to;
        }

    }

    function getSuffix($file)
    {
        $arr = explode(".", $file);
        return $arr[count($arr) - 1];
    }

}