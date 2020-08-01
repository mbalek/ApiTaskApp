<?php
/**
 * Created by PhpStorm.
 * User: Ikki
 * Date: 31.07.2020
 * Time: 13:38
 */

namespace App\Service;


use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TagService
{
    /**
     * @var TagRepository
     */
    private $tagRepo;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator,
                                LoggerInterface $logger, TagRepository $tagRepository)
    {
        $this->tagRepo = $tagRepository;
        $this->em = $em;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * @param string $tags
     * @return array
     */
    public function checkIfTagsExists(string $tags): array
    {
        $tagsReturn = [];
        if(empty($tags) || '' === $tags)
            throw new ValidatorException('String for tags cannot be empty');

        $tagsArray = array_filter(explode(' ', preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $tags)));

        foreach($tagsArray as $tagString){
            array_push($tagsReturn, $this->checkIfTagExists($tagString));
        }

        return $tagsReturn;
    }

    /**
     * @param string $tagString
     * @return Tag|null
     */
    public function checkIfTagExists(string $tagString):?Tag
    {
        $tag = $this->tagRepo->findOneBy(['name' => $tagString]);

        if(null === $tag)
            $tag = $this->insertTag($tagString);

        return $tag;
    }

    /**
     * @param string $tagString
     * @return Tag
     */
    public function insertTag(string $tagString):Tag
    {
        $tag = new Tag();
        $tag->setName($tagString);

        $errors = $this->validator->validate($tag);
        if(count($errors) > 0){
            $errorString = (string) $errors;
            $this->logger->warning('Failed to create Tag entity cause of validation errors below');
            $this->logger->warning($errorString);

            throw new ValidatorException('Failed to create Setting entity \n'.$errorString);
        }

        $this->em->persist($tag);

        $this->logger->info('Creation of entity Setting successful');

        return $tag;
    }

}