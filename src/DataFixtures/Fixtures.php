<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Need;
use App\Entity\Feeling;
use App\Entity\Action;
use App\Entity\Block;
use App\Entity\UserNeed;
use App\Entity\UserAction;
use App\Entity\Notification;
use App\Entity\Subscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Fixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // -------------------
        // USERS
        // -------------------
        $user1 = (new User())
            ->setEmail('user1@example.com')
            ->setPassword('1234');
        $manager->persist($user1);
        $this->addReference('user1', $user1);

        $user2 = (new User())
            ->setEmail('user2@example.com')
            ->setPassword('1234');
        $manager->persist($user2);
        $this->addReference('user2', $user2);

        // -------------------
        // NEEDS
        // -------------------
        $need1 = (new Need())
            ->setTitle('Besoin de clarté')
            ->setDescription('Avoir une vision claire de mes objectifs')
            ->setType('fondamental');
        $manager->persist($need1);
        $this->addReference('need1', $need1);

        $need2 = (new Need())
            ->setTitle('Besoin de repos')
            ->setDescription('Pouvoir récupérer et me ressourcer')
            ->setType('physique');
        $manager->persist($need2);
        $this->addReference('need2', $need2);

        // -------------------
        // FEELINGS
        // -------------------
        $feeling1 = (new Feeling())
            ->setTitle('Stress')
            ->setDescription('Tension mentale ou physique')
            ->setEmotion('anxiété')
            ->setColor('red');
        $feeling1->addNeed($need1);
        $manager->persist($feeling1);
        $this->addReference('feeling1', $feeling1);

        $feeling2 = (new Feeling())
            ->setTitle('Joie')
            ->setDescription('Sentiment de satisfaction')
            ->setEmotion('bonheur')
            ->setColor('yellow');
        $feeling2->addNeed($need2);
        $manager->persist($feeling2);
        $this->addReference('feeling2', $feeling2);

        // -------------------
        // ACTIONS
        // -------------------
        $action1 = (new Action())
            ->setTitle('Meditation')
            ->setDescription('10 min de méditation')
            ->setType('mental')
            ->setDuration(10)
            ->setIsDoableNow(true);
        $manager->persist($action1);
        $this->addReference('action1', $action1);

        $action2 = (new Action())
            ->setTitle('Sieste')
            ->setDescription('15 min de repos')
            ->setType('physique')
            ->setDuration(15)
            ->setIsDoableNow(true);
        $manager->persist($action2);
        $this->addReference('action2', $action2);

        // -------------------
        // BLOCKS
        // -------------------
        $block1 = (new Block())
            ->setTitle('Bloc mental')
            ->setDescription('Exercices pour le mental')
            ->setType('mental')
            ->setIcon('brain.png');
        $block1->addAction($action1);
        $manager->persist($block1);
        $this->addReference('block1', $block1);

        $block2 = (new Block())
            ->setTitle('Bloc physique')
            ->setDescription('Exercices pour le corps')
            ->setType('physique')
            ->setIcon('muscle.png');
        $block2->addAction($action2);
        $manager->persist($block2);
        $this->addReference('block2', $block2);

        // -------------------
        // USERNEEDS
        // -------------------
        $userNeed1 = (new UserNeed())
            ->setUser($user1)
            ->setNeed($need1)
            ->setPriority(3)
            ->setScore(50);
        $manager->persist($userNeed1);
        $this->addReference('userNeed1', $userNeed1);

        $userNeed2 = (new UserNeed())
            ->setUser($user2)
            ->setNeed($need2)
            ->setPriority(4)
            ->setScore(70);
        $manager->persist($userNeed2);
        $this->addReference('userNeed2', $userNeed2);

        // -------------------
        // USERACTIONS
        // -------------------
        $userAction1 = (new UserAction())
            ->setUser($user1)
            ->setAction($action1)
            ->setUserNeed($userNeed1)
            ->setStatus('à faire')
            ->setIsChecked(false)
            ->setStartDate(new \DateTime());
        $manager->persist($userAction1);
        $this->addReference('userAction1', $userAction1);

        $userAction2 = (new UserAction())
            ->setUser($user2)
            ->setAction($action2)
            ->setUserNeed($userNeed2)
            ->setStatus('fait')
            ->setIsChecked(true)
            ->setStartDate(new \DateTime());
        $manager->persist($userAction2);
        $this->addReference('userAction2', $userAction2);

        // -------------------
        // NOTIFICATIONS
        // -------------------
        $notification1 = (new Notification())
            ->setTitle('Action à faire')
            ->setMessage('N’oublie pas de méditer')
            ->setType('rappel')
            ->setUser($user1)
            ->setUserAction($userAction1)
            ->setReceivedAt(new \DateTimeImmutable())
            ->setIsRead(false);
        $manager->persist($notification1);

        $notification2 = (new Notification())
            ->setTitle('Action complétée')
            ->setMessage('Ta sieste est terminée')
            ->setType('info')
            ->setUser($user2)
            ->setUserAction($userAction2)
            ->setReceivedAt(new \DateTimeImmutable())
            ->setIsRead(true);
        $manager->persist($notification2);

        $manager->flush();
    }
}
