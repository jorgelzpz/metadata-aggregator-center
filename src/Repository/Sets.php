<?php

namespace RedIRIS\MetadataCenter\Repository;

use RedIRIS\MetadataCenter\MetadataSet;

class Sets
{
    private $db;

    /**
     * @param mixed $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Return all configured sets
     *
     * @return MetadataSet[]
     */
    public function findAll()
    {
        $sql = "SELECT * FROM sets ORDER BY name";
        $raw_result = $this->db->fetchAll($sql);
        $result = [];

        foreach ($raw_result as $set) {
            $result[] = $this->buildMetadataSet($set);
        }

        return $result;
    }

    /**
     * Returns a set by id
     *
     * @param int $id
     * @return MetadataSet
     * @throws \Exception if set is not found
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM sets WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
        $raw_result = $stmt->fetch();

        if ($raw_result === null) {
            throw new \Exception('Set with id '.$id.' not found');
        }

        return $this->buildMetadataSet($set);
    }

    /**
     * Returns a set by name
     *
     * @param string $name
     * @return MetadataSet
     * @throws \Exception if set is not found
     */
    public function findByName($name)
    {
        $sql = "SELECT * FROM sets WHERE name = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $name);
        $stmt->execute();
        $raw_result = $stmt->fetch();

        if ($raw_result === false) {
            throw new \Exception('Set with name '.$name.' not found');
        }

        return $this->buildMetadataSet($raw_result);
    }

    public function save(MetadataSet $set)
    {
        if ($set->getId() === null) {
            $data = [
                'name' => $set->getName(),
                'url' => $set->getUrl(),
                'filter' => $set->getFilter(),
            ];
            $this->db->insert('sets', $data);
        } else {
            $data = [
                'name' => $set->getName(),
                'url' => $set->getUrl(),
                'filter' => $set->getFilter(),
            ];
            $this->db->update('sets', $data, [ 'id' => $set->getId() ]);
        }
    }

    /**
     * Deletes a set
     *
     * @param MetadataSet $set
     * @throws \Exception if set is not found
     */
    public function delete(MetadataSet $set)
    {
        $this->db->delete('sets', [ 'id' => $set->getId() ]);
    }

    /**
     * Gets an array of parameters and builds a MetadataSet object
     *
     * @param Array $data
     * @return MetadataSet
     */
    protected function buildMetadataSet(Array $data)
    {
        $result = new MetadataSet(
            $data['url'],
            $data['filter'],
            $data['name']
        );

        if (isset($data['id'])) {
            $result->setId($data['id']);
        }

        return $result;
    }
}
