<?php

namespace App\Command;


use App\Service\Optad360Service;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ApiOptad360CallCommand extends Command
{
    private $optad360Service;

    protected static $defaultName = 'optad360:api';

    public function __construct(Optad360Service $optad360Service)
    {
        $this->optad360Service = $optad360Service;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Make a call for optad360 api')
                ->setHelp('This command allows you to make whatever call to api you like. First argument is a
                method of request, second argument is a URL after the base one for api, next arguments are params')
                ->addArgument('method',  InputArgument::REQUIRED, 'Methods of call(GET,POST...)')
                ->addArgument('url' , InputArgument::REQUIRED, 'The part of URL after base api path')
                ->addArgument('param1',  InputArgument::OPTIONAL, 'Param1 for query, format paramName=value')
                ->addArgument('param2' , InputArgument::OPTIONAL, 'Param2 for query, format paramName=value')
                ->addArgument('param3',  InputArgument::OPTIONAL, 'Param3 for query, format paramName=value')
                ->addArgument('param4' , InputArgument::OPTIONAL, 'Param4 for query, format paramName=value')
                ->addArgument('param5',  InputArgument::OPTIONAL, 'Param5 for query, format paramName=value')
                ->addArgument('param6' , InputArgument::OPTIONAL, 'Param6 for query, format paramName=value')
                ->addArgument('param7',  InputArgument::OPTIONAL, 'Param7 for query, format paramName=value')
                ->addArgument('param8' , InputArgument::OPTIONAL, 'Param8 for query, format paramName=value');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        switch (strtoupper($input->getArgument('method'))){
            case 'GET':
                $output->writeln('Your request is in progress');
                $result = $this->optad360Service->handleGetCommand($farray = array_filter($input->getArguments()));
                (is_bool($result)) ? $output->writeln('Your request completed successful') : $output->writeln(print_r($result));
                break;
            case 'POST':
                //TODO if needed
                break;
            case 'PUT':
                //TODO if needed
                break;
            case 'DELETE':
                //TODO if needed
                break;
        }


        return 1;
    }

}