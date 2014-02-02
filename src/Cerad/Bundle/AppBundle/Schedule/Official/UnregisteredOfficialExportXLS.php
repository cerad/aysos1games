<?php
namespace Cerad\Bundle\AppBundle\Schedule\Official;

/* ============================================
 * Basic referee schedule exporter
 */
class UnregisteredOfficialExportXLS
{
    protected $counts = array();

    protected $widths = array
    (
        'Game' =>  6, 'Game#' =>  6,

        'Name' => 26, 'Pos' => 6
    );
    protected $center = array
    (
        'Game',
    );
    public function __construct($excel)
    {
        $this->excel = $excel;
    }
    protected function setHeaders($ws,$map,$row = 1)
    {
        $col = 0;
        foreach(array_keys($map) as $header)
        {
            $ws->getColumnDimensionByColumn($col)->setWidth($this->widths[$header]);
            $ws->setCellValueByColumnAndRow($col++,$row,$header);

            if (in_array($header,$this->center) == true)
            {
                // Works but not for multiple sheets?
                // $ws->getStyle($col)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
        }
        return $row;
    }
    protected function setRow($ws,$map,$person,&$row)
    {
        $row++;
        $col = 0;
        foreach($map as $propName)
        {
            $ws->setCellValueByColumnAndRow($col++,$row,$person[$propName]);
        }
        return $row;
    }
    /* =======================================================================
     * Main entry point
     */
    public function generate($games)
    {
        // Spreadsheet
        $ss = $this->excel->newSpreadSheet();
        $ws = $ss->getSheet(0);

        $ws->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $ws->getPageSetup()->setPaperSize  (\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $ws->getPageSetup()->setFitToPage(true);
        $ws->getPageSetup()->setFitToWidth(1);
        $ws->getPageSetup()->setFitToHeight(0);
        $ws->setPrintGridLines(true);

        $this->processOfficials($ws,$games);

        // Output
        $ss->setActiveSheetIndex(0);
        $objWriter = $this->excel->newWriter($ss); // \PHPExcel_IOFactory::createWriter($ss, 'Excel5');

        ob_start();
        $objWriter->save('php://output'); // Instead of file name
        return ob_get_clean();
    }
    /* ===========================================================
     * Add a sheet listing current assignments for each official
     * Should probably get moved to it's own processor
     */
    protected function processOfficials($ws,$games)
    {
        // Make a sorted array of officials from games
        $officials = array();
        foreach($games as $game)
        {
            foreach($game->getOfficials() as $official)
            {
                $name = $official->getPersonNameFull();
                if ($name)
                {
                  if ($official->getPersonGuid() === NULL )
                  {
                      $officials[$name][] = $official;
                  }
                }
            }
        }
        ksort($officials);

        // Generate
        $this->generateUnregisteredOfficials($ws,$officials);
    }
    /* ========================================================
     * Generates the officials listing
     */
    public function generateUnregisteredOfficials($ws,$officials)
    {
        // Only the keys are currently being used
        $map = array(
            'Name'     => 'name',
            'Pos'      => 'pos',
            'Game'     => 'game',
        );
        $ws->setTitle('Unregistered Officials');

        $row = $this->setHeaders($ws,$map);

        foreach($officials as $officialSlots)
        {
            foreach($officialSlots as $official) {

            $row++;
            $col = 0;

            // Official
            $name = $official->getPersonNameFull();
            $pos  = $official->getRole();
            $game = $official->getGame();

            $ws->setCellValueByColumnAndRow($col++,$row,$name);
            $ws->setCellValueByColumnAndRow($col++,$row,$pos);
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getNum());

        }}
        return;
    }
}
?>
