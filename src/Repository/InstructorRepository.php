<?php

namespace App\Repository;

use App\Entity\Instructor;
use App\Helper\SingletonTrait;
use \Faker\Factory;
use App\Repository\RepositoryInterface;

class InstructorRepository implements RepositoryInterface
{
    use SingletonTrait;

    private $firstname;
    private $lastname;

    /**
     * InstructorRepository constructor.
     */
    public function __construct()
    {
        $this->firstname = Factory::create()->firstName;
        $this->lastname = Factory::create()->lastName;
    }

    /**
     * @param int $id
     *
     * @return Instructor
     */
    public function getById($id): Instructor
    {
        // DO NOT MODIFY THIS METHOD
        return new Instructor(
            $id,
            $this->firstname,
            $this->lastname
        );
    }

    /**
     * return firstname
     */
    public function getFirstname()
    {
        return $this->firstname;
    }
    /**
     * return lastname
     */
    public function getLastname()
    {
        return $this->lastname;
    }
}
