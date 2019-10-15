<?php


namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Trainer;

class AppFixtures extends Fixture
{
    private $faker;

    private $name = [
                'Антон',
                'Александр',
                'Игорь',
                'Иван',
                'Владимир',
                'Дмитрий',
                'Олег',
                'Станислав',
                'Алексей',
                'Сергей',
    ];

    private $sureName = [
        'Иванов',
        'Петров',
        'Сидоров',
        'Петухов',
        'Шанин',
        'Скворцов',
        'Синицын',
        'Игорев',
        'Скворцов',
        'Ухин',
    ];

    private $middleName = [
        'Иванович',
        'Петрович',
        'Андреевич',
        'Владимирович',
        'Максимович',
        'Александрович',
        'Матвеевич',
        'Николаевич',
        'Сергеевич',
        'Алексеевич',
    ];

    private $level = [
        'стажер',
        'опытный',
        'профессионал'

    ];

    private $rate = ['пол-ставки','полная'];


    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->loadTrainers($manager);
    }

    public function loadTrainers(ObjectManager $manager)
    {
        $numberRows = 30000;
        $blockNum = 1000;
        $hundredSpace = str_pad(' ', 100, ' ');
        $countLoop = $numberRows/$blockNum;
        for ($j=0 ; $j<$countLoop ; $j++) {
            for ($i = 0; $i < $blockNum; $i++) {
                $trainer = new Trainer();

                $genName = $this->sureName[$this->faker->randomDigit] . ' ';
                $genName .= $this->name[$this->faker->randomDigit] . ' ';
                $genName .= $this->middleName[$this->faker->randomDigit];

                $dateStart = $this->faker->dateTimeBetween('-15 years', 'now');
                $dateEnd = $this->faker->optional(0.1)->dateTimeBetween('-15 years', 'now');

                $trainer->setName($genName);
                $trainer->setLevel($this->faker->randomElement($this->level));
                $trainer->setPhone($this->faker->phoneNumber);
                $trainer->setEmail($this->faker->email);
                $trainer->setDateStart($dateStart);
                $trainer->setDateEnd($dateEnd);
                $trainer->setRate($this->faker->randomElement($this->rate));
                $trainer->setSchedule(
                    [
                        'weekend' => $this->faker->boolean,
                        'personal' => $this->faker->boolean,
                        'salary' => $this->faker->numberBetween(5, 10) * 100,
                        'groups' => $this->faker->boolean,
                        'notifyHours' => $this->faker->numberBetween(1, 24),
                    ]
                );

                $manager->persist($trainer);
            }

            $startTime = microtime(true);
            $manager->flush();
            $finishTime = microtime(true);
            $timeExecBlock = $finishTime  - $startTime;
            $fullTime = $timeExecBlock * ($countLoop);
            $estimatedTime = (int)round($fullTime - ($timeExecBlock * $j));

            $minute = (int)round($estimatedTime / 60);
            $countPound = (100/$countLoop)*($j+1);
            $progressBar = str_pad(str_pad('#', $countPound, '#'), 100, '.');


            echo " Осталось примерно $minute минут $estimatedTime секунд $progressBar \r";

        }
        echo "$hundredSpace $hundredSpace \r Успешно завершено! \n";

    }

}