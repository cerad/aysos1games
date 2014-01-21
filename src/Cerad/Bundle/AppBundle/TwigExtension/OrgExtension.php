<?php
namespace Cerad\Bundle\AppBundle\TwigExtension;

class OrgExtension extends \Twig_Extension
{
    protected $env;
    protected $orgRepo;

    public function getName()
    {
        return 'cerad_org_extension';
    }
    public function __construct($orgRepo)
    {
        $this->orgRepo = $orgRepo;
    }
    public function initRuntime(\Twig_Environment $env)
    {
        parent::initRuntime($env);
        $this->env = $env;
    }
    protected function escape($string)
    {
        return twig_escape_filter($this->env,$string);
    }
    public function getFilters()
    {
        return array(
            'cerad_org_sar' => new \Twig_Filter_Method($this, 'sar'),
        );
    }
    // Return section area region
    public function sar($orgKey)
    {
        $org = $this->orgRepo->find($orgKey);

        if (!$org) return substr($orgKey,4);

        return (int) substr($org->getParent(),5,2) . '/' . substr($org->getParent(),7,1) . '/' . (int) substr($orgKey,5);

    }

}
?>
