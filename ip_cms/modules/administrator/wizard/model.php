<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\administrator\wizard;



class Model{



    public function disableWizardTip($id)
    {
        global $parametersMod;
        $parametersMod->setValue('administrator', 'wizard', 'options', 'tip_'.$id, false);
    }

    public function getTipIds()
    {
        return array(
            'dragWidget',
            'dropWidget',
            'changeWidgetContent',
            'confirmWidget',
            'publish'
        );
    }

}
