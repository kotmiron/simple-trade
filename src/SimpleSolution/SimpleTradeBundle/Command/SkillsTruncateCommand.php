<?php

namespace SimpleSolution\SimpleTradeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SimpleSolution\SimpleTradeBundle\Model\SkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\SkillsParentsModel;

class SkillsTruncateCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('skills:truncate')
            ->setDescription('Truncate skills tables')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $SkillsModel = new SkillsModel($em);
        $SkillsModel->truncate();

        $SkillsParentsModel = new SkillsParentsModel($em);
        $SkillsParentsModel->truncate();
    }

}