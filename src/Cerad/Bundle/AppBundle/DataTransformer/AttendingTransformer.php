<?php
namespace Cerad\Bundle\AppBundle\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class AttendingTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
      switch ($value) {
        case "no":
            $desc = "No";
            break;
        case "yes":
            $desc =  "Yes";
            break;
        case "yesx":
            $desc =  "Yes";
            break;
        case "maybe":
            $desc =  "Maybe";
            break;
        case "we1":
            $desc =  "League only";
            break;
        case "we2":
            $desc =  "All-Star/Extra only";
            break;
        case "we12":
            $desc = "Both weekends";
            break;
      }

      return $desc;
    }

    public function reverseTransform($value)
    {
      switch ($value) {
        case "No":
            $desc = "no";
            break;
        case "Yes":
            $desc =  "yes";
            break;
        //case "yesx":
        //    $desc =  "Yes";
        //    break;
        case "Maybe":
            $desc =  "maybe";
            break;
        case "League only":
            $desc =  "we1";
            break;
        case "All-Star/Extra only":
            $desc =  "we2";
            break;
        case "Both weekends":
            $desc = "we12";
            break;
      }

      return $desc;
    }
}
?>
