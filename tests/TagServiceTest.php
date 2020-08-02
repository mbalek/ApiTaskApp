<?php

namespace App\Tests;


use App\Entity\Tag;
use App\Service\TagService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TagServiceTest extends KernelTestCase
{
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

    public function setUp()
    {
        self::bootKernel();
        $this->em  =  static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->validator = static::$kernel->getContainer()->get('validator');
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testCheckIfTagsExists()
    {
        $tag = new Tag();
        $tag->setName('TestTag1');
        $tag2 = new Tag();
        $tag2->setName('TestTag1');

        $tagService = $this->createPartialMock(TagService::class,['checkIfTagExists']);
        $tagService->method('checkIfTagExists')
            ->willReturn($tag);

        $result = [];
        array_push($result, $tag);
        $this->assertEquals($result, $tagService->checkIfTagsExists('TestTag1'));

        $result=[];
        array_push($result, $tag);
        array_push($result, $tag2);
        $this->assertEquals($result, $tagService->checkIfTagsExists('TestTag1 TestTag2'));

        $this->expectException(ValidatorException::class);
        $tagService->checkIfTagsExists('');

    }

    public function testCheckIfTagExists()
    {
        $tagString = 'mastercuriosidadesbr.com';
        $tag = new Tag();
        $tag->setName('TestTag1');
        $tagInDb = $this->em->getRepository(Tag::class)->findOneBy(['name' => $tagString]);

        $tagService = $this->getMockBuilder(TagService::class)
            ->setConstructorArgs([$this->em, $this->validator, $this->logger, $this->em->getRepository(Tag::class) ])
            ->setMethods(['insertTag'])
            ->getMock();
        $tagService->method('insertTag')
            ->willReturn($tag);

        $this->assertEquals($tagInDb, $tagService->checkIfTagExists($tagString));
        $this->assertEquals($tag, $tagService->checkIfTagExists('TestTag1'));
    }

    public function testInsertTag()
    {
        $tagService = $this->getMockBuilder(TagService::class)
            ->setMethods(['none'])
            ->setConstructorArgs([$this->em, $this->validator, $this->logger, $this->em->getRepository(Tag::class) ])
            ->getMock();

        $seed = str_split('abcdefghijklmnopqrstuvwxyz'
            .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'); //
        $rand = '';
        foreach (array_rand($seed, 5) as $k) $rand .= $seed[$k];

        $tag = new Tag();
        $tag->setName($rand);
        $this->assertEquals($tag, $tagService->insertTag($rand));

        $this->expectException(ValidatorException::class);
        $tagService->insertTag('');
    }

}