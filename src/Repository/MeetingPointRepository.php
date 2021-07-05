<?php

namespace App\Repository;

use App\Entity\MeetingPoint;
use App\Helper\SingletonTrait;
use \Faker\Factory;
use App\Repository\RepositoryInterface;

class MeetingPointRepository implements RepositoryInterface
{
    use SingletonTrait;

    private $url;
    private $name;

    /**
     * SiteRepository constructor.
     *
     */
    public function __construct()
    {
        // DO NOT MODIFY THIS METHOD
        $this->url = Factory::create()->url;
        $this->name = Factory::create()->city;
    }

    /**
     * @param int $id
     *
     * @return MeetingPoint
     */
    public function getById($id): MeetingPoint
    {
        // DO NOT MODIFY THIS METHOD
        return new MeetingPoint($id, $this->url, $this->name);
    }
}
