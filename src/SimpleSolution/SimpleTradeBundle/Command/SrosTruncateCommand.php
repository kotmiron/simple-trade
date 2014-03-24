<?php

namespace SimpleSolution\SimpleTradeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SimpleSolution\SimpleTradeBundle\Model\SrosModel;

class SrosTruncateCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('sros:truncate')
            ->setDescription('Truncate sros tables')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $session = $this->getContainer()->get('session');

        $srosModel = new SrosModel($em, $session, null);
        $srosModel->truncate();
    }

}