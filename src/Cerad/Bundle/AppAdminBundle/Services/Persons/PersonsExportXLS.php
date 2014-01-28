<?php
namespace Cerad\Bundle\AppAdminBundle\Services\Persons;

use Cerad\Bundle\PersonBundle\DataTransformer\PhoneTransformer;
use Cerad\Bundle\AppBundle\DataTransformer\AttendingTransformer;

class PersonsExportXLS
{
    protected $excel;
    protected $orgRepo;

    public function __construct($excel,$orgRepo)
    {
        $this->excel   = $excel;
        $this->orgRepo = $orgRepo;

        $this->phoneTransformer = new PhoneTransformer();
        $this->attendingTransformer = new AttendingTransformer();

    }
    protected function setColumnWidths($ws,$widths)
    {
        $col = 0;
        foreach($widths as $width)
        {
            $ws->getColumnDimensionByColumn($col++)->setWidth($width);
        }
    }
    protected function setRowValues($ws,$row,$values)
    {
        $col = 0;
        foreach($values as $value)
        {
            $ws->setCellValueByColumnAndRow($col++,$row,$value);
        }
    }
    /* ================================================================
     * Master sheet of everyone
     */
    protected function generateAllSheet($ws,$project,$officials)
    {
        $ws->setTitle('All');

        $headers = array_merge(
            array(
                'ID','Status','Name','Email','Cell Phone',
                'AYSO ID','Section','Area','Region','Badge','MY','Safe Haven',
                'Verified','Want Mentor','Upgrading',
                'Will Attend','Referee',
            )
        );
        $this->writeHeaders($ws,1,$headers);
        $row = 2;

        foreach($officials as $person)
        {
            $name        = $person->getName();
          //$address     = $person->getAddress();
            $personFed   = $person->getFed($project->getFedRole());
            $cert        = $personFed->getCertReferee();
            $plan        = $person->getPlan($project->getId());
            $basic       = $plan->getBasic();

          //if ($basic['refereeing'] == 'no') continue;

            $values = array();
            $values[] = $plan->getId();
            $values[] = $plan->getStatus();
          //$values[] = null; // $plans->getDateTimeCreated()->format('Y-m-d H:i');
            $values[] = $name->full;
            $values[] = $person->getEmail();
            $values[] = $this->phoneTransformer->transform($person->getPhone());

          //$gender = $person->getGender();
          //$age    = $person->getAge();
          //$gage   = $gender . $age;
          //$values[] = $gage;

          //$city = $address->city . ', ' . $address->state;
          //$values[] = $city;

            $orgKey = $personFed->getOrgKey();
            $org = $this->orgRepo->find($orgKey);
            $section = $org ? (int) substr($org->getParent(),5,2) : null;
            $area = $org ? substr($org->getParent(),7,1) : null;
            $region = (int) substr($orgKey,5,4);

            $values[] = substr($personFed->getFedKey(),4);
            $values[] = $section;
            $values[] = $area;
            $values[] = $region;
            //$values[] = substr($personFed->getOrgKey(),4);
            $values[] = $cert->getBadge();
            $values[] = $personFed->getMemYear();
            $values[] = $personFed->getCertSafeHaven()->getBadge();
            $values[] = $personFed->getPersonVerified();

            $values[] = $basic['wantMentor'];
            $values[] = $cert->getUpgrading();

            /* ========================================================
             * You can test the attending value (we1,we2,we12 to break this
             * Into attendingLeague and attendingASExtra
             * 27 Jan 2014: RR: Created DataTransformer to do this.
             */
            $values[] = $this->attendingTransformer->transform($basic['attending']);
            $values[] = $basic['refereeing'];

            $this->setRowValues($ws,$row++,$values);
        }

        $usedrange = $ws->calculateWorksheetDimension();
        $ws->getStyle($usedrange)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Done
        return;
    }
    /* ================================================================
     * Master sheet of referees
     */
    protected function generateOfficialsSheet($ws,$project,$officials)
    {
        $ws->setTitle('Officials');

        $headers = array_merge(
            array(
                'ID','Status','Official','Email','Cell Phone',
                'AYSO ID','Section','Area','Region','Badge','MY','Safe Haven',
                'Verified','Want Mentor','Upgrading',
                'Will Attend','Referee',
            )
        );
        $this->writeHeaders($ws,1,$headers);
        $row = 2;

        foreach($officials as $person)
        {
            $name        = $person->getName();
          //$address     = $person->getAddress();
            $personFed   = $person->getFed($project->getFedRole());
            $cert        = $personFed->getCertReferee();
            $plan        = $person->getPlan($project->getId());
            $basic       = $plan->getBasic();

            if ($basic['refereeing'] == 'no') continue;

            $values = array();
            $values[] = $plan->getId();
            $values[] = $plan->getStatus();
          //$values[] = null; // $plans->getDateTimeCreated()->format('Y-m-d H:i');
            $values[] = $name->full;
            $values[] = $person->getEmail();
            $values[] = $this->phoneTransformer->transform($person->getPhone());

          //$gender = $person->getGender();
          //$age    = $person->getAge();
          //$gage   = $gender . $age;
          //$values[] = $gage;

          //$city = $address->city . ', ' . $address->state;
          //$values[] = $city;

            $orgKey = $personFed->getOrgKey();
            $org  = $this->orgRepo->find($orgKey);
            //$area = $orgKey ? substr($org->getParent(),4) : null;
            $section = $org ? (int) substr($org->getParent(),5,2) : null;
            $area = $org ? substr($org->getParent(),7,1) : null;
            $region = (int) substr($orgKey,5,4);

            $values[] = substr($personFed->getFedKey(),4);
            $values[] = $section;
            $values[] = $area;
            $values[] = $region;
            //$values[] = substr($personFed->getOrgKey(),4);
            $values[] = $cert->getBadge();
            $values[] = $personFed->getMemYear();
            $values[] = $personFed->getCertSafeHaven()->getBadge();
            $values[] = $cert->getBadgeVerified();

            $values[] = $basic['wantMentor'];
            $values[] = $cert->getUpgrading();

            // See note about getting these values from attending
            $values[] = $this->attendingTransformer->transform($basic['attending']);
            $values[] = $basic['refereeing'];

            $this->setRowValues($ws,$row++,$values);
        }

        $usedrange = $ws->calculateWorksheetDimension();
        $ws->getStyle($usedrange)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Done
        return;
    }
    /* =============================================================
     * The availability
     */
    protected function generateAvailSheet($ws,$project,$officials)
    {
        $ws->setTitle('Availability');

        $headers = array_merge(
            array(
                'Official','Email','Cell Phone','Age',
                'Section','Area','Region',
                'Badge','Level','LE CR','LE AR','Assess','Upgrading',
                'Team Aff','Team Desc',
            ),
            $this->availabilityDaysHeaders
        );

        $this->writeHeaders($ws,1,$headers);
        $row = 2;

        foreach($officials as $person)
        {
            $personFed   = $person->getFed($project->getFedRoleId());
            $cert        = $personFed->getCertReferee();
            $plan        = $person->getPlan($project->getId());
            $basic       = $plan->getBasic();

            $values = array();
            $values[] = $person->getName()->full;
            $values[] = $person->getEmail();
            $values[] = $this->phoneTransformer->transform($person->getPhone());

            $gender = $person->getGender();
            $age    = $person->getAge();
            $gage   = $gender . $age;
            $values[] = $gage;

            $values[] = $basic['refereeLevel'];
            $values[] = $basic['comfortLevelCenter'];
            $values[] = $basic['comfortLevelAssist'];

            $values[] = $basic['requestAssessment'];
            $values[] = $cert->getUpgrading();
            $values[] = $basic['teamClubAffilation'];
            $values[] = $basic['teamClubName'];

            foreach($basic['availabilityDays'] as $value)
            {
                $values[] = $value;
            }
            $this->setRowValues($ws,$row++,$values);
        }

        $usedrange = $ws->calculateWorksheetDimension();
        $ws->getStyle($usedrange)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Done
        return;
    }
    /* ==========================================================
     * Put the notes on their own sheer
     * Formatting tends to be ugly
     */
    protected function generateNotesSheet($ws,$project,$officials)
    {
        $ws->setTitle('Notes');

        $headers = array(
            'Status','Official','Email','Cell Phone',
            'Section','Area','Region',
            'Badge','Verified','Notes');

        $this->writeHeaders($ws,1,$headers);
        $row = 2;

        foreach($officials as $person)
        {
            $personFed   = $person->getFed($project->getFedRoleId());
            $cert        = $personFed->getCertReferee();
            $plan        = $person->getPlan($project->getId());
            $basic       = $plan->getBasic();

            $orgKey = $personFed->getOrgKey();
            $org  = $this->orgRepo->find($orgKey);
            //$area = $orgKey ? substr($org->getParent(),4) : null;
            $section = $org ? (int) substr($org->getParent(),5,2) : null;
            $area = $org ? substr($org->getParent(),7,1) : null;
            $region = (int) substr($orgKey,5,4);

            if ($basic['refereeing'] == 'no') continue;

            $values = array();
            $values[] = $plan->getStatus();
            $values[] = $person->getName()->full;
            $values[] = $person->getEmail();
            $values[] = $this->phoneTransformer->transform($person->getPhone());
            $values[] = $section;
            $values[] = $area;
            $values[] = $region;
            $values[] = $cert->getBadge();
            $values[] = $cert->getBadgeVerified();
            $values[] = $basic['notes'];

            $this->setRowValues($ws,$row++,$values);
        }

        $usedrange = $ws->calculateWorksheetDimension();
        $ws->getStyle($usedrange)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Done
        return;
    }
    /* ===================================================================
     * Deal with widths and such
     */
    protected $widths = array
    (
        'ID' => 6,
        'Status'       => 8,
        'Applied Date' => 16,
        'AYSO ID'    => 12,
        'Official'   => 24,
        'Email'      => 24,
        'Cell Phone' => 14,
        'Age'        =>  4,
        'Badge'      => 12,
        'Verified'   =>  4,
        'Notes'      => 72,
        'Home City'  => 16,
        'USSF State' =>  4,
        'Region'     =>  7,
        'Area'       =>  8,
        'Section'    =>  7,
      //'AV Fri'     =>  8,
      //'AV Sat'     =>  8,
      //'AV Sun'     =>  8,
      //'LO Fri'     =>  6,
      //'LO Sat'     =>  6,
        'LO With'    =>  8,
        'TR From'    =>  8,
        'TR With'    =>  8,
        'Assess'     =>  8,
        'Want Mentor'  =>  8,
        'Upgrading'  =>  8,
        'Team Aff'   => 10,
        'Team Desc'  => 10,
        'Level'      => 14,
        'LE CR'      =>  6,
        'LE AR'      =>  6,

        'AttendLeague'  => 8,
        'AttendASExtra'  => 8,
        'Referee' => 8,
    );
    protected function writeHeaders($ws,$row,$headers)
    {
        $col = 0;
        foreach($headers as $header)
        {
            if (isset($this->widths[$header])) $width = $this->widths[$header];
            else                               $width = 16;

            $ws->getColumnDimensionByColumn($col)->setWidth($width);
            $ws->setCellValueByColumnAndRow($col,$row,$header);
            $col++;
        }
    }
    /* ==========================================================
     * Main entry point
     */
    public function generate($project,$officials)
    {
        $this->ss = $ss = $this->excel->newSpreadSheet();

        $si = 0;

        $this->generateAllSheet      ($ss->createSheet($si++),$project,$officials);
        $this->generateOfficialsSheet($ss->createSheet($si++),$project,$officials);
        $this->generateNotesSheet    ($ss->createSheet($si++),$project,$officials);
      //$this->generateLodgingSheet  ($ss->createSheet($si++),$project,$officials);
      //$this->generateAvailSheet    ($ss->createSheet($si++),$project,$officials);

        // Finish up
        $ss->setActiveSheetIndex(1);
        return $ss;
    }
    /* =======================================================
     * Called by controller to get the content
     */
    protected $ss;

    public function getBuffer($ss = null)
    {
        if (!$ss) $ss = $this->ss;
        if (!$ss) return null;

        $objWriter = $this->excel->newWriter($ss); // \PHPExcel_IOFactory::createWriter($ss, 'Excel5');

        ob_start();
        $objWriter->save('php://output'); // Instead of file name

        return ob_get_clean();
    }

}
?>
