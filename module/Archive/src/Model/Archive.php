<?php
namespace Archive\Model;

class Archive
{
    public $id;
    public $name;
    public $change_datetime;
    public $cnt;
    public $size;

    public function exchangeArray(array $data)
    {
        $this->id = !empty($data['id']) ? $data['id'] : null;
        $this->name = !empty($data['name']) ? $data['name'] : null;
        $this->change_datetime = !empty($data['change_datetime']) ? $data['change_datetime'] : null;
        $this->cnt = !empty($data['cnt']) ? $data['cnt'] : null;
        $this->size = !empty($data['size']) ? $data['size'] : null;
    }

    public function toArray() {
        $files = [];
        $filename = './data/zip/'.$this->id.'.zip';
        if (file_exists($filename)) {
            $zip = new \ZipArchive();
            $ret = $zip->open($filename);
            for ($i=0; $i<$zip->numFiles;$i++) {
                $info = $zip->statIndex($i);
                $files[] = $info;
            }
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'change_datetime' => $this->change_datetime,
            'cnt' => $this->cnt,
            'size' => $this->size,
            'files' => $files
        ];
    }
}
