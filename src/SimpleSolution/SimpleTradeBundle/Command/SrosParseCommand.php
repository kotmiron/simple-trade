<?php

namespace SimpleSolution\SimpleTradeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SimpleSolution\SimpleTradeBundle\Model\SrosModel;
use SimpleSolution\SimpleTradeBundle\Model\SrosContentModel;
use SimpleSolution\SimpleTradeBundle\Model\SrosTypesModel;
use PHPExcel;
use PHPExcel_IOFactory;

class SrosParseCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('sros:parse')
            ->setDescription('Parse sros from xls file')
            ->addArgument('type', InputArgument::REQUIRED, 'type')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to *.xls file')
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
        $session = $this->getContainer()->get('session');
        $acl = null;

        $srosModel = new SrosModel($em, $session, $acl);
        $SrosTypesModel = new SrosTypesModel($em);
        $srosType = $SrosTypesModel->findByPK($type);

        set_time_limit(1800);
        ini_set('memory_liit', '128M');

        $chunkSize = 2000;
        $startRow = 2;
        $exit = false;
        $empty_value = 0;

        $objReader = PHPExcel_IOFactory::createReaderForFile($file);
        $objReader->setReadDataOnly(true);

        $chunkFilter = new chunkReadFilter();
        $objReader->setReadFilter($chunkFilter);
        while( !$exit ) {
            $chunkFilter->setRows($startRow, $chunkSize);
            $objPHPExcel = $objReader->load($file);
            $objPHPExcel->setActiveSheetIndex(0);
            $objWorksheet = $objPHPExcel->getActiveSheet();

            for( $i = $startRow; $i < $startRow + $chunkSize; $i++ ) {
                $value = trim(htmlspecialchars($objWorksheet->getCellByColumnAndRow(0, $i)->getValue()));
                $title = trim(htmlspecialchars($objWorksheet->getCellByColumnAndRow(1, $i)->getValue()));
                $email = trim(htmlspecialchars($objWorksheet->getCellByColumnAndRow(3, $i)->getValue()));

                if (empty($value)) {
                    $empty_value++;
                } else {
                    $srosModel->create(array(
                        'title' => $title,
                        'email' => $email ), array( 'type' => $srosType ));
                }
                if ($empty_value == 3) {
                    $exit = true;
                    continue;
                }
            }
            $objPHPExcel->disconnectWorksheets();
            unset($objPHPExcel);
            $startRow += $chunkSize;
        }
    }

}

class chunkReadFilter implements \PHPExcel_Reader_IReadFilter
{
    private $_startRow = 0;
    private $_endRow = 0;

    /**  Set the list of rows that we want to read  */
    public function setRows($startRow, $chunkSize)
    {
        $this->_startRow = $startRow;
        $this->_endRow = $startRow + $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        //  Only read the heading row, and the rows that are configured in $this->_startRow and $this->_endRow
        if (($row == 1) || ($row >= $this->_startRow && $row < $this->_endRow)) {
            return true;
        }
        return false;
    }

}