<?php
namespace NeutronStars\Neutrino\Core;

use NeutronStars\Database\QueryExecutor;

class Model
{
    private string $table;
    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * @return array|object[]|mixed[]
     */
    public function all(): array
    {
        return $this->createQuery()
            ->select('*')
            ->getResults();
    }

    /**
     * @param $id
     * @param string $column
     * @return array|object|mixed|null
     */
    public function findById($id, string $column = 'id')
    {
        return $this->createQuery()
            ->select('*')
            ->where($column . '=:id')
            ->setParameters([
                ':id' => $id
            ])->getResult();
    }

    public function deleteById($id, string $column = 'id'): void
    {
        $this->createQuery()
            ->delete()
            ->where($column . '=:id')
            ->setParameters([
                ':id' => $id
            ])->execute();
    }

    public function count(): int
    {
        return Kernel::get()->getDatabase()
            ->fetchColumn($this->createQuery()->select('COUNT(*)')->build());
    }

    protected function createQuery(string $alias = null): QueryExecutor
    {
        return Kernel::get()->getDatabase()->query($this->table . ($alias !== null ? ' ' . $alias : ''));
    }
}
