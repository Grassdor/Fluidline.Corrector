<?php

namespace App\Services;

use App\Entity\Validator;
use Doctrine\Persistence\ObjectManager;

class DbCompare
{
    private string $serializedPath;

    private ObjectManager $objectManager;

    private string $filePath;
    
    public function setFilePath(string $path) : void
    {
        $this->filePath = $path;
    }

    public function getFilePath() : string
    {
        return $this->filePath;
    }

    public function getSerializedPath() : string
    {
        return $this->serializedPath;
    }

    public function setSerializedPath(string $path) : void
    {
        $this->serializedPath = $path;
    }

    public function setObjectManager(ObjectManager $manager) : void
    {
        $this->objectManager = $manager;
    }

    public function getObjectManager() : ObjectManager
    {
        return $this->objectManager;
    }

    public function compareEmails($email, $listname) : void
    {
        $serializedPath = $this->getSerializedPath();

        /** @var ObjectManager $manager */
        $manager = $this->getObjectManager();

        $validatorRepository = $manager->getRepository(Validator::class);

        /** @var Validator $validator */
        $validator = $validatorRepository->findOneBy(['email' => $email]);

        if (is_null($validator)) {
            $validator = new Validator();
            $validator->setCreated(new \DateTime());
            $validator->setEmail($email);
            $listpath = $serializedPath . $listname . "/";
            if (!is_dir($listpath)) {
                mkdir($listpath, recursive: true);
            }
            file_put_contents($listpath . uniqid(), serialize($validator));
        }
    }

    public function correctEmails() : void
    {
        $filepath = $this->getFilePath();

        $fileinfo = pathinfo($filepath);

        $f = fopen($filepath, 'r');

        while ($row = fgetcsv($f)) {
            $this->compareEmails($row[0], $fileinfo['filename']);
        }
        fclose($f);

    }
}