<?php

namespace App\Context;
use App\Entity\Learner;
use App\Entity\MeetingPoint;
use App\Helper\SingletonTrait;
use \Faker\Factory;

class ApplicationContext
{
    use SingletonTrait;

    /**
     * @var MeetingPoint
     */
    private $currentSite;
    /**
     * @var Learner
     */
    private $currentUser;

    protected function __construct()
    {
        $faker = Factory::create();
        $this->currentSite = new MeetingPoint($faker->randomNumber(), $faker->url, $faker->city);
        $this->currentUser = new Learner($faker->randomNumber(), $faker->firstName, $faker->lastName, $faker->email);
    }

    public function getCurrentSite()
    {
        return $this->currentSite;
    }

    public function getCurrentUser()
    {
        return $this->currentUser;
    }
}
