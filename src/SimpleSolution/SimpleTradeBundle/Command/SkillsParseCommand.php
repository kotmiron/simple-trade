<?php

namespace SimpleSolution\SimpleTradeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SimpleSolution\SimpleTradeBundle\Entity\Skills;
use SimpleSolution\SimpleTradeBundle\Entity\SkillsParents;
use SimpleSolution\SimpleTradeBundle\Model\SkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\SkillsParentsModel;
use SimpleSolution\SimpleTradeBundle\Model\SrosTypesModel;

class SkillsParseCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('skills:parse')
            ->setDescription('Parse skills from text file')
            ->addArgument('type', InputArgument::REQUIRED, 'type')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to *.txt file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        $file = $input->getArgument('file');

        if (false === file_exists($file)) {
            $output->writeln('<error>File "' . $file . '" not found</error>');
            return;
        }
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $SrosTypesModel = new SrosTypesModel($em);
        $SrosType = $SrosTypesModel->findByPK($type);

        $a = explode("\n", file_get_contents($file));

        $SkillsModel = new SkillsModel($em);

        $SkillsParentsModel = new SkillsParentsModel($em);

        $parents = array( );

        foreach( $a as $v ) {
            $v = trim($v);

            if (empty($v)) {
                continue;
            }

            $i = strpos($v, ' ');
            $number = trim(substr($v, 0, $i), '.');
            $title = trim(substr($v, $i));

            $a = explode('.', $number);

            if (1 === count($a)) {

                if (!isset($parents[ $a[ 0 ] ])) {
                    $SkillsParents = new SkillsParents;
                    $SkillsParents->setTitle($title);
                    $SkillsParents->setType($SrosType);
                    $SkillsParentsModel->createByEntity($SkillsParents);
                    $parents[ $a[ 0 ] ] = $SkillsParents->getId();
                }
            } else {
                $Skills = new Skills;
                $Skills->setTitle($title);
                $Skills->setParent($SkillsParentsModel->findByPK($parents[ $a[ 0 ] ]));
                $SkillsModel->createByEntity($Skills);
            }
        }
    }

}