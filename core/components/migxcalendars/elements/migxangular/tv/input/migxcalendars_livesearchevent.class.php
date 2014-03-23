<?php

/**
 * @package modx
 * @subpackage processors.element.tv.renders.mgr.input
 */
class modTemplateVarInputRenderLivesearchEvent extends modTemplateVarInputRenderMigxFe {
    public function getTemplate() {
        return 'migxcalendars_livesearchevent.tpl';
    }
    
    public function process($value, array $params = array()) {

    }  
    
    public function getTemplateRoot() {
        return dirname(dirname(dirname(dirname(dirname(__file__))))) . '/migxangulartemplates/web/element/tv/renders/input/';
    }           
        
    

}

return 'modTemplateVarInputRenderLivesearchEvent';
