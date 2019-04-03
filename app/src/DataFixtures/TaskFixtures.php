<?php
/**
 * Task fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class TaskFixtures.
 */
class TaskFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(50, 'tasks', function ($i) {
            $task = new Task();
            $task->setTitle($this->faker->sentence);
            $task->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $task->setUpdatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $task->setCategory($this->getRandomReference('categories'));

            return $task;
        });

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     *
     * @return array Array of dependencies
     */
    public function getDependencies(): array
    {
        return [CategoryFixtures::class];
    }
}