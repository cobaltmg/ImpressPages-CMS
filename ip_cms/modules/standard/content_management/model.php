<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;
if (!defined('CMS')) exit;

require_once(__DIR__.'/event_widget.php');

class Model{
    static private $widgetObjects = null;
    
    public static function generateBlock($blockName, $revision, $managementState) {
    	global $site;

    	$widgets = self::getBlockWidgetRecords($blockName, $revision['id']);
    	
    	$widgetsHtml = array();
    	foreach ($widgets as $key => $widget) {
    		$widgetsHtml[] = self::_generateWidgetPreview($widget, $managementState);
    	}

    	$data = array (
    		'widgetsHtml' => $widgetsHtml,
    		'blockName' => $blockName,    		
    		'revision' => $revision,
    		'managementState' => $managementState
    	);
    	
    	$answer = \Ip\View::create('standard/content_management/view/block.php', $data)->render();
    	return $answer;
    }
    
    public static function getBlockWidgetRecords($blockName, $revisionId){
        $sql = "
        	SELECT w.*, rtw.revisionId, rtw.position, rtw.blockName, rtw.visible 
        	FROM
        		`".DB_PREF."m_content_management_revision_to_widget` rtw,
        		`".DB_PREF."m_content_management_widget` w
        	WHERE
        		rtw.widgetId = w.id AND
        		rtw.blockName = '".mysql_real_escape_string($blockName)."' AND
        		rtw.revisionId = ".(int)$revisionId."
     		ORDER BY `position` ASC
        ";    
        $rs = mysql_query($sql);
        if (!$rs){
            throw new \Exception('Can\'t get widgets '.$sql.' '.mysql_error());
        }
        
        $answer = array();
        
        while ($lock = mysql_fetch_assoc($rs)) {
            $answer[] = $lock;
        }
            	
    	return $answer;
    }
    

    
    public static function duplicateRevision($oldRevisionId, $newRevisionId) {
        $sql = "
            SELECT * 
            FROM
                `".DB_PREF."m_content_management_revision_to_widget` rtw
            WHERE
                rtw.revisionId = ".(int)$oldRevisionId."
            ORDER BY `position` ASC
        ";    
        
        $rs = mysql_query($sql);
        if (!$rs){
            throw new \Exception('Can\'t get revision data '.$sql.' '.mysql_error());
        }        
        
        while ($lock = mysql_fetch_assoc($rs)) {
            
            $dataSql = '';
            
            foreach ($lock as $key => $value) {
                if ($dataSql != '') {
                    $dataSql .= ', ';    
                }
                
                if ($key != 'revisionId' && $key != 'id' ) {
                    $dataSql .= " `".$key."` = '".mysql_real_escape_string($value)."' ";
                } 
            }
            
            $insertSql = "
                INSERT INTO
                    `".DB_PREF."m_content_management_revision_to_widget`
                SET
                    ".$dataSql.",
                    `revisionId` = ".(int)$newRevisionId."                     
                    
            ";    
            
            $insertRs = mysql_query($insertSql);
            if (!$insertRs){
                throw new \Exception('Can\'t get revision data '.$insertSql.' '.mysql_error());
            }        
        }        
        
    }
    
    public static function getAvailableWidgetObjects() {
        global $dispatcher;
        
        if (self::$widgetObjects !== null) {
            return self::$widgetObjects;
        }
        
        $event = new EventWidget(null, 'contentManagement.collectWidgets', null);
        $dispatcher->notify($event);
        
        $widgetObjects = $event->getWidgets();
        
        self::$widgetObjects = $widgetObjects;
        return self::$widgetObjects;
    }
    
    /**
     * 
     * Enter description here ...
     * @param unknown_type $widgetName
     * @return \Modules\standard\content_management\Widget
     */
    public static function getWidgetObject($widgetName) {
        global $dispatcher;
        
        $widgetObjects = self::getAvailableWidgetObjects();
        
        if (isset($widgetObjects[$widgetName])) {
            return $widgetObjects[$widgetName];
        } else {
            return false;    
        }

    }
    
    public static function getWidgetRecord($widgetId) {
        $sql = "
        	SELECT * FROM `".DB_PREF."m_content_management_widget`
        	WHERE `id` = ".(int)$widgetId."
        ";    
        
        $rs = mysql_query($sql);
        if (!$rs){
            throw new \Exception('Can\'t find widget '.$sql.' '.mysql_error());
        }
        
        if ($lock = mysql_fetch_assoc($rs)) {
            return $lock;
        } else {
            return false;
        }
    }

    public static function generateWidgetPreview($widgetId, $managementState) {
        $widgetRecord = self::getWidgetRecord($widgetId);
        return self::_generateWidgetPreview($widgetRecord, $managementState);
    }
    
    private static function _generateWidgetPreview($widgetRecord, $managementState) {
        $widgetData = json_decode($widgetRecord['data'], true);
        if (!is_array($widgetData)) {
            $widgetData = array();    
        }
        
        $widgetObject = self::getWidgetObject($widgetRecord['name']);
        
        if (!$widgetObject) {
            throw new \Exception('Widget does not exist. Widget name: '.$widgetRecord['name']);
        } 
        
        $previewHtml = $widgetObject->previewHtml($widgetRecord['id'], $widgetData);
        
        $data = array (
            'html' => $previewHtml,
            'widgetRecord' => $widgetRecord,
        	'managementState' => $managementState
        );
        $answer = \Ip\View::create('standard/content_management/view/widget_preview.php', $data)->render();
        return $answer;    
    }
    
    public static function generateWidgetManagement($widgetId) {
        $widgetRecord = self::getWidgetRecord($widgetId);
        return self::_generateWidgetManagement($widgetRecord);
    }
    
    private static function _generateWidgetManagement($widgetRecord) {
        $widgetData = json_decode($widgetRecord['data'], 1);
        
        if (!is_array($widgetData)) {
            $widgetData = array();    
        }
        
        $widgetObject = self::getWidgetObject($widgetRecord['name']);
        
        if (!$widgetObject) {
            throw new \Exception('Widget does not exist. Widget name: '.$widgetRecord['name']);
        } 
        
        $managementHtml = $widgetObject->managementHtml($widgetRecord['id'], $widgetData);
        
        $data = array (
            'managementHtml' => $managementHtml,
            'widgetRecord' => $widgetRecord
        );
        $answer = \Ip\View::create('standard/content_management/view/widget_management.php', $data)->render();
        return $answer;    
    }
    
    
    /**
     * 
     * Enter description here ...
     * @param $revisionId
     * @param $blockName
     * @param int $newPosition Real position of widget starting with 0
     */
    private static function _calcWidgetPositionNumber($revisionId, $widgetId, $newBlockName, $newPosition) {
        $allWidgets = self::getBlockWidgetRecords($newBlockName, $revisionId);
        
        $widgets = array();
        
        foreach ($allWidgets as $widgetKey => $widget) {
            if ($widget['id'] != $widgetId) {
                $widgets[] = $widget;
            }    
        }
        
        if (count($widgets) == 0) {
            $positionNumber = 0;    
        } else {
            if ($newPosition == 0) {
                $positionNumber = $widgets[0]['position'] - 40;                
            } else {
                if ($newPosition >= count($widgets)) {
                    $positionNumber = $widgets[count($widgets) - 1]['position'] + 40;            
                } else {
                    $positionNumber = ($widgets[$newPosition - 1]['position'] + $widgets[$newPosition]['position']) / 2;            
                }
            }
        }
        return $positionNumber;
    }
    
    /**
     * 
     * Enter description here ...
     * @param $revisionId
     * @param int $position Real position of widget starting with 0
     * @param $blockName
     * @param $widgetName
     * @param $layout
     * @throws \Exception
     */
    public static function createWidget($revisionId, $position, $blockName, $widgetName, $layout) {
        
        $positionNumber = self::_calcWidgetPositionNumber($revisionId, null, $blockName, $position);
        
        
        
        $sql = "
        	insert into
        		".DB_PREF."m_content_management_widget
        	set
        		`name` = '".mysql_real_escape_string($widgetName)."',
        		`layout` = '".mysql_real_escape_string($layout)."',
        		`created` = ".time()."
        ";
        
        $rs = mysql_query($sql);
        
        if (!$rs) {
            throw new \Exception('Can\'t create new widget '.$sql.' '.mysql_error());
        }
        
        $widgetId = mysql_insert_id();
        
            $sql = "
        	insert into
        		".DB_PREF."m_content_management_revision_to_widget
        	set
                `position` = '".mysql_real_escape_string($positionNumber)."',
                `blockName` = '".mysql_real_escape_string($blockName)."',
                `visible` = 1,
        	    `revisionId` = ".(int)$revisionId.",
        		`widgetId` = ".(int)$widgetId."
        ";
        
        $rs = mysql_query($sql);
        
        if (!$rs) {
            throw new \Exception('Can\'t associated revision to widget '.$sql.' '.mysql_error());
        }        
        
        return $widgetId;
    }
    
    public static function setWidgetData($widgetId, $data) {
        $sql = "
            UPDATE `".DB_PREF."m_content_management_widget`
            SET
                `data` = '".mysql_real_escape_string(json_encode($data))."'
            WHERE `id` = ".(int)$widgetId."
        ";    
        
        $rs = mysql_query($sql);
        if (!$rs){
            throw new \Exception('Can\'t update widget '.$sql.' '.mysql_error());
        }
        
        return true; 
    }
    
    
    
    public static function deleteWidget($widgetId, $revisionId) {
        $sql = "
            DELETE FROM `".DB_PREF."m_content_management_revision_to_widget`
            WHERE
                `widgetId` = ".(int)$widgetId." AND
                `revisionId` = ".(int)$revisionId." 
                
        ";    
        
        $rs = mysql_query($sql);
        if (!$rs){
            throw new \Exception('Can\'t update widget '.$sql.' '.mysql_error());
        }
        
        return true; 
    }
        
    
    public static function moveWidget($revisionId, $widgetId, $newPosition, $newBlockName) {
        
        $positionNumber = self::_calcWidgetPositionNumber($revisionId, $widgetId, $newBlockName, $newPosition);
        
        $sql = "
            UPDATE `".DB_PREF."m_content_management_revision_to_widget`
            SET
                `position` = '".$positionNumber."',
                `blockName` = '".mysql_real_escape_string($newBlockName)."'
            WHERE `widgetId` = ".(int)$widgetId." AND `revisionId` = ".(int)$revisionId."
        ";    
        $rs = mysql_query($sql);
        if (!$rs){
            throw new \Exception('Can\'t update widget '.$sql.' '.mysql_error());
        }
        
        return true;         
    }
    
}