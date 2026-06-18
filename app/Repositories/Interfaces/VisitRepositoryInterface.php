<?php
namespace App\Repositories\Interfaces;

interface VisitRepositoryInterface
{
    public function all();
    public function find(int $id);
    public function findByVisitNumber(string $visitNumber);
    public function create(array $data);
    public function update(int $id, array $data);
}