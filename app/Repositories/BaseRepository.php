<?php
/**
 * Author david you
 * Date 2024/5/22
 * Time 18:59
 */

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository as BaseRepo;
use Prettus\Repository\Exceptions\RepositoryException;

class BaseRepository extends BaseRepo
{

    public function model()
    {
        // TODO: Implement model() method.
    }

    /**
     * @param array $data
     * @throws RepositoryException
     * @return mixed
     */
    public function insert(array $data = [])
    {
        $this->applyCriteria();
        $this->applyScope();

        $results = $this->model->insert($data);

        $this->resetModel();

        return $this->parserResult($results);
    }

    /**
     * @param array $data
     * @return mixed
     * @throws RepositoryException
     */
    public function cursor()
    {
        $this->applyCriteria();
        $this->applyScope();

        $results = $this->model->cursor();

        $this->resetModel();

        return $this->parserResult($results);
    }
}
