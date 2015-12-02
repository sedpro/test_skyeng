<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Console;
use App\Helper\Time;
use Application\Entity\Teacher as TeacherEntity;

class CliController extends AbstractActionController
{
    const CHUNK_SIZE = 1000;

    /**
     * Creates all needed classes and fills them with random data
     */
    public function installAction()
    {
        Time::check();

        $this->createTables();
        Console::getInstance()->writeLine('Tables created in ' . Time::check() . ' sec.');

        $pupilService = $this->getPupilService();
        $count = $this->generateItems(
            $pupilService,
            $this->getRequest()->getParam('pupils', 100000),
            ['email', 'birthday'],
            function ($item) {
                return [
                    'name' => $item['full'],
                    'email' => $item['email'],
                    'birthday' => $item['birthday'],
                    'level' => \Application\Entity\Pupil::$levels[rand(0, 5)],
                ];
            }
        );
        Console::getInstance()->writeLine($count . ' pupils generated in ' . Time::check() . ' sec.');

        $teachersCount = $this->getRequest()->getParam('teachers', 10000);
        $teacherService = $this->getTeacherService();
        $this->generateItems(
            $teacherService,
            $teachersCount,
            ['phone'],
            function ($item) {
                $gender = $item['gender'] === \NameGenerator\Gender::GENDER_MALE
                    ? TeacherEntity::GENDER_MALE
                    : TeacherEntity::GENDER_FEMALE;

                return [
                    'gender' => $gender,
                    'name'   => $item['full'],
                    'phone'  => $item['phone'],
                ];
            }
        );
        Console::getInstance()->writeLine($teachersCount . ' teachers generated in ' . Time::check() . ' sec.');

        $pupilMaxId = $pupilService->getMaxId();
        $teacherMaxId = $teacherService->getMaxId();

        $teacherPupilService = $this->getTeacherPupilService();
        $linksCount = 0;
        for ($teacherId=1; $teacherId<$teacherMaxId; $teacherId++) {
            $except = [];
            for ($j=0; $j<rand(0,3); $j++) {
                $pupil_id = rand(0, $pupilMaxId);
                if (in_array($pupil_id, $except)) {
                    continue;
                }
                $except[] = $pupil_id;

                $teacherPupilService->insert([
                    'teacher_id' => $teacherId,
                    'pupil_id' => $pupil_id,
                ]);
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin",

            "DROP TABLE IF EXISTS `teacher_pupil`;
            CREATE TABLE `teacher_pupil` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `teacher_id` int(11) NOT NULL,
                `pupil_id` int(11) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `teacher_id_pupil_id` (`teacher_id`,`pupil_id`),
                KEY `pupil_id` (`pupil_id`) )
            ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin",
        ];

        foreach ($tables as $sql) {
            $this->getServiceLocator()->get('dbAdapter')->createStatement($sql)->execute();
        }
    }

    /**
     * @return \Application\Service\Teacher
     */
    private function getTeacherService()
    {
        return $this->getServiceLocator()->get('TeacherService');
    }

    /**
     * @return \Application\Service\Pupil
     */
    private function getPupilService()
    {
        return $this->getServiceLocator()->get('PupilService');
    }

    /**
     * @return \Application\Service\TeacherPupil
     */
    private function getTeacherPupilService()
    {
        return $this->getServiceLocator()->get('TeacherPupilService');
    }

    /**
     * @param $service \App\Service\MysqlStorageble
     * @param $itemsCount int
     * @param $additionalFields array
     * @param $callable callable
     * @return int
     * @throws \Exception
     */
    private function generateItems($service, $itemsCount, $additionalFields, $callable)
    {
        $count = 0;
        $generator = new \NameGenerator\Generator;
        while ($count < $itemsCount) {
            $currentCount = min(self::CHUNK_SIZE, $itemsCount - $count);
            $items = $generator->get($currentCount, $additionalFields);
            $items = array_map($callable, $items);

            try{
                $count += $service->bulkInsert($items);
            } catch(\Exception $e) {}
        }

        return $count;
    }


}
