<?php
namespace Cerad\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//  Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Cerad\Component\Excel\Excel;

class PrintCommand extends ContainerAwareCommand
{
    protected $commandName = 'command';
    protected $commandDesc = 'Command Description';

    protected function configure()
    {
        $this
            ->setName       ('print:path')
            ->setDescription('print path')
            ->addArgument   ('path', InputArgument::REQUIRED, 'path');
          //->addArgument   ('truncate',   InputArgument::OPTIONAL, 'Truncate')
        ;
    }
    protected function getService  ($id)   { return $this->getContainer()->get($id); }


    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $params = array();

        $params['filepath'] = $input->getArgument('path');
        $levelRepo      = $this->getService('cerad_level.level_repository');

        $results = $levelRepo->process($params);

        $output->write($results->__toString());

        return;
    }
}
?>