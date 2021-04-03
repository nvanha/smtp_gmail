<?php
    function getAllFiles() {
        $allFiles = array();
        $allDirs = glob('uploads/*');
        foreach ($allDirs as $dir) {
            $allFiles = array_merge($allFiles, glob($dir . '/'));
        }
        return $allFiles;
    }

    function uploadFiles($uploadedFiles) {
        $files = array();
        $errors = array();
        foreach ($uploadedFiles as $key => $values) {
            foreach ($values as $index => $value) {
                $files[$index][$key] = $value;
            }
        }
        $uploadPath = "uploads/" . date('d-m-Y', time());
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
        foreach($files as $file) {
            $file = validateUploadFile($file, $uploadPath);
            if ($file != false) {
                if (move_uploaded_file($file['tmp_name'], $uploadPath . '/' . $file['name'])) {
                    $returnFiles[] = str_replace('./', '/', $uploadPath) . '/' . $file['name'];
                }
            } else {
                $errors[] = "The file " . basename($file['name']) . " isn't valid.";
            }
        } 
        return array(
            'errors' => $errors,
            'uploaded_files' => $returnFiles
        );
    }

    function validateUploadFile($file, $uploadPath) {
        if ($file['size'] > 2 * 1024 * 1024) {
            return false;
        }
        $validTypes = array("jpg", "png", "jpeg");
        $fileType = substr($file['name'], strrpos($file['name'], ".") + 1);
        if (!in_array($fileType, $validTypes)) {
            return false;
        }
        $num = 1;
        $fileName = substr($file['name'], 0, strrpos($file['name'], '.'));
        while (file_exists($uploadPath . "/" . $fileName . "." . $fileType)) {
            $fileName = $fileName . "(". $num . ")";
            $num++;
        }
        $file['name'] = $fileName . "." . $fileType;
        return $file;
    }
?>