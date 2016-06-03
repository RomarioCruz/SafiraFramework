<?php

class UploadHelper {

    protected $path = 'public/uploads/';
    protected $file;
    protected $fileName;
    protected $fileTmpName;

    public function setPath($path) {
        $this->path = $path;
        return $this;
    }

    public function setFile($file) {
        $this->file = $file;
        $this->setFileName();
        $this->setFileTmpName();
        return $this;
    }

    protected function setFileName() {
        $this->fileName = $this->file['name'];
    }

    protected function setFileTmpName() {
        $this->fileTmpName = $this->file['tmpName'];
    }

    public function upload() {
        if (move_uploaded_file($this->fileTmpName, $_SERVER['DOCUMENT_ROOT'] . $this->path . $this->fileName)) {
            return true;
        } else {
            return false;
        }
    }

}
