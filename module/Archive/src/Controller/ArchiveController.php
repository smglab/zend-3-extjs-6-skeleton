<?php
namespace Archive\Controller;

use Archive\Model\ArchiveTable;
use Archive\Form\ArchiveForm;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class ArchiveController extends AbstractRestfulController
{
    private $table;

    public function __construct(ArchiveTable $table){
        $this->table = $table;
    }

    public function getList()
    {
        $offset = $this->params()->fromQuery('start', 0);
        $limit = $this->params()->fromQuery('limit', 10);
        $sort = $this->params()->fromQuery('sort', false);
        if ($sort) {
            $sort = Json::decode($sort);
        }

        $result = $this->table->fetchAll($offset, $limit, $sort);
        if (!$result->count()) {
            return new JsonModel(['message' => 'Archive not found', 'items' => []]);
        }
        $data = [];
        foreach($result as $i) {
            $data[] = $i;
        }
        return new JsonModel([
            'items' => $data,
            'total' => $this->table->getTotal(),
            'success' => true
        ]);
    }

    public function get($id)
    {
        if (!$id) {
            return new JsonModel(['message' => 'Id not found', 'success' => false]);
        }

        $result = $this->table->getById($id);

        if (!is_object($result)) {
            return new JsonModel(['message' => 'Archive not found', 'success' => false]);
        }
        return new JsonModel(['item' => $result->toArray(), 'success' => true]);
    }

    public function create($data)
    {
        $request = $this->getRequest();
        $form = new ArchiveForm();
        $post = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );

        $form->setData($post);
        if ($form->isValid()) {
            $data = $form->getData();
            if (!count($data['files'])) {
                return new JsonModel(['message' => 'Not found files', 'success' => false]);
            }
            $size = 0;
            foreach( $data['files'] as $key => $val ) {
                $size += $val['size'];
            }
            if ($size > 10485760) {
                return new JsonModel(['message' => 'Error. Size to big', 'success' => false]);
            }
            $id = $this->table->saveData($data, $size, count($data['files']));
            if (!file_exists('./data/zip')) {
                mkdir('./data/zip', 0777, true);
            }
            $zip = new \ZipArchive();
            $filename = './data/zip/'.$id.'.zip';
            $ret = $zip->open($filename, \ZipArchive::CREATE|\ZipArchive::OVERWRITE);
            foreach( $data['files'] as $key => $val ) {
                $name = $val['name'];
                if ($zip->statName($name)) {
                    $name = uniqid() . '_' . $name;
                }
                $zip->addFile($val['tmp_name'], $name);
            }
            $zip->close();
            foreach( $data['files'] as $key => $val ) {
                unlink($val['tmp_name']);
            }
            return new JsonModel(['message' => 'Success!', 'success' => true]);
        } else {
            return new JsonModel(['message' => 'Form validate error', 'success' => false]);
        }
    }

    public function update($id, $data)
    {
        $request = $this->getRequest();
        $form = new ArchiveForm();
        $post = array_merge_recursive(
            ['id' => $id],
            $data,
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );
        $form->setData($post);
        if ($form->isValid()) {
            $data = $form->getData();
            $size = 0;
            $cnt = count($data['files']);
            $fileDelete = explode(',', $data['filedelete']);
            if (!file_exists('./data/zip')) {
                mkdir('./data/zip', 0777, true);
            }
            $zip = new \ZipArchive();
            $filename = './data/zip/'.$id.'.zip';
            $ret = $zip->open($filename, \ZipArchive::CREATE);
            if ($zip->numFiles == count($fileDelete) && !$cnt) {
                $zip->close();
                return new JsonModel(['message' => 'Error. Empty archive', 'success' => false]);
            }
            for ($i=0; $i<$zip->numFiles;$i++) {
                $info = $zip->statIndex($i);
                if (!in_array($info['name'], $fileDelete)) {
                    $size += $info['comp_size'];
                    $cnt += 1;
                }
            }
            foreach( $data['files'] as $key => $val ) {
                $size += $val['size'];
            }
            if ($size > 10485760) {
                $zip->close();
                return new JsonModel(['message' => 'Error. Size to big', 'success' => false]);
            }

            $this->table->saveData($data, $size, $cnt);

            for ($i=0; $i<$zip->numFiles;$i++) {
                $info = $zip->statIndex($i);
                if (in_array($info['name'], $fileDelete)) {
                    $zip->deleteIndex($i);
                }
            }
            foreach( $data['files'] as $key => $val ) {
                $name = $val['name'];
                if ($zip->statName($name)) {
                    $name = uniqid() . '_' . $name;
                }
                $zip->addFile($val['tmp_name'], $name);
            }
            $zip->close();
            foreach( $data['files'] as $key => $val ) {
                unlink($val['tmp_name']);
            }
            return new JsonModel(['message' => 'Success!', 'success' => true]);
        } else {
            return new JsonModel(['message' => 'Form validate error', 'success' => false]);
        }
    }

    public function delete($id)
    {
        if (!$id) {
            return new JsonModel(['message' => 'Id not found', 'success' => false]);
        }

        $result = $this->table->getById($id);

        if ($result) {
            $this->table->deleteById($id);
            $filename = './data/zip/'.$id.'.zip';
            if (file_exists($filename)) {
                unlink($filename);
            }
        } else {
            return new JsonModel(['message' => 'Archive not found', 'success' => false]);
        }

        return new JsonModel([
            'message' => 'Archive deleted successful!', 'success' => true
        ]);
    }
}
