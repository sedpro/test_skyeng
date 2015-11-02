<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Console;
use \Application\Helper\Time;

class CliController extends AbstractActionController
{
    /**
     * Creates all needed classes and filles them with dummie data
     */
    public function installAction()
    {
        Time::check();

        $nameGenerator = new \NameGenerator\Generator;

        $this->createTables();

        $teachersCount = $this->getRequest()->getParam('teachers', 10000);
        $pupilsCount = $this->getRequest()->getParam('pupils', 100000);

        /** @var \Application\Model\Teacher $teacherTable */
        $teacherTable = $this->getServiceLocator()->get('TeacherTable');

        /** @var \Application\Model\Pupil $pupilTable */
        $pupilTable = $this->getServiceLocator()->get('PupilTable');

        /** @var \Application\Model\TeacherPupil $teacherPupilTable */
        $teacherPupilTable = $this->getServiceLocator()->get('TeacherPupilTable');

        Console::getInstance()->writeLine('Tables created in ' . Time::check() . ' sec.');

        $pupils = $nameGenerator->get($pupilsCount, ['email', 'birthday']);
        foreach ($pupils as $randomPupil) {
            $level = \Application\Entity\Pupil::$levels[rand(0, count(\Application\Entity\Pupil::$levels)-1)];

            /** @var \Application\Entity\Pupil $pupil */
            $pupil = $pupilTable->getEntity();
            $pupil
                ->setName($randomPupil['first'] . ' ' . $randomPupil['last'])
                ->setEmail($randomPupil['email'])
                ->setBirthday($randomPupil['birthday'])
                ->setLevel($level);
            $pupilTable->insert($pupil);
        }

        Console::getInstance()->writeLine($pupilsCount . ' pupils generated in ' . Time::check() . ' sec.');

        $teachers = $nameGenerator->get($teachersCount, ['phone']);
        foreach ($teachers as $randomTeacher) {
            $gender = $randomTeacher['gender'] == \NameGenerator\Gender::GENDER_MALE
                ? \Application\Entity\Teacher::GENDER_MALE
                : \Application\Entity\Teacher::GENDER_FEMALE;

            /** @var \Application\Entity\Teacher $teacher */
            $teacher = $teacherTable->getEntity();
            $teacher
                ->setName($randomTeacher['first'] . ' ' . $randomTeacher['last'])
                ->setGender($gender)
                ->setPhone($randomTeacher['phone']);
            $teacherTable->insert($teacher);
        }

        Console::getInstance()->writeLine($teachersCount . ' teachers generated in ' . Time::check() . ' sec.');

        $pupilMaxId = $pupilTable->getMaxId();
        $teacherMaxId = $teacherTable->getMaxId();

        $linksCount = 0;
        for ($teacherId=1; $teacherId<$teacherMaxId; $teacherId++) {
            $except = [];
            for ($j=0; $j<rand(0,3); $j++) {
                $pupil_id = rand(0, $pupilMaxId);
                if (in_array($pupil_id, $except)) {
                    continue;
                }
                $except[] = $pupil_id;

                $link = $teacherPupilTable->getEntity();
                $link->teacher_id = $teacherId;
                $link->pupil_id = $pupil_id;
                $teacherPupilTable->insert($link);
                $linksCount++;
            }
        }
        Console::getInstance()->writeLine($linksCount . ' links generated in ' . Time::check() . ' sec.');
    }

    /**
     * creates all needed tables;
     */
    private function createTables()
    {
        $tables = [
            "DROP TABLE IF EXISTS `pupil`;
            CREATE TABLE `pupil` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) COLLATE utf8_bin NOT NULL,
                `email` varchar(255) COLLATE utf8_bin NOT NULL,
                `birthday` date NOT NULL,
                `level` enum('a1','a2','b1','b2','c1','c2') COLLATE utf8_bin NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `name` (`name`),
                KEY `birthday` (`birthday`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin",
            "DROP TABLE IF EXISTS `teacher`;
            CREATE TABLE `teacher` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) COLLATE utf8_bin NOT NULL,
                `gender` enum('m','f') COLLATE utf8_bin NOT NULL,
                `phone` varchar(255) COLLATE utf8_bin NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_bin",
            "DROP TABLE IF EXISTS `teacher_pupil`;
            CREATE TABLE `teacher_pupil` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `teacher_id` int(11) NOT NULL,
                `pupil_id` int(11) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `teacher_id_pupil_id` (`teacher_id`,`pupil_id`),
                KEY `pupil_id` (`pupil_id`) )
            ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COLLATE=utf8_bin",
        ];

        foreach ($tables as $sql) {
            $this->getServiceLocator()->get('dbAdapter')->createStatement($sql)->execute();
        }
    }
}
