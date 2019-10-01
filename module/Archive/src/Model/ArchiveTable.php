<?php
namespace Archive\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Select;

class ArchiveTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function fetchAll(int $offset, int $limit, $sort)
    {
        $select = $this->tableGateway->getSql()->select();
        if ($sort) {
            $select->order($sort[0]->property.' '.$sort[0]->direction);
        }
        $select->limit($limit);
        $select->offset($offset);
        return $this->tableGateway->selectWith($select);
    }

    public function getTotal() {
        $select = $this->tableGateway->select();
        return $select->count();
    }

    public function getById(int $id) {
        return $this->tableGateway->select(['id' => $id])->current();
    }

    public function deleteById(int $id) {
        $this->tableGateway->delete(['id' => $id]);
    }

    public function saveData($form, $size, $cnt) {
        $now = new \DateTime('NOW', new \DateTimeZone('Europe/Moscow'));
        $data = [
            'name' => $form['name'],
            'change_datetime' => $now->format('Y-m-d H:h:i'),
            'cnt' => $cnt,
            'size' => $size
        ];
        if ($form['id']) {
            $this->tableGateway->update($data, ['id' => $form['id']]);
            return $form['id'];
        } else {
            $this->tableGateway->insert($data);
            return $this->tableGateway->adapter->getDriver()->getLastGeneratedValue("archive_id_seq");
        }
    }
}
