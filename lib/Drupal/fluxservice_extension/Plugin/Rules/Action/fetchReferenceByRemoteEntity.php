<?php

/**
 * @file
 * Contains fetchReferenceByTrelloId.
 */

namespace Drupal\fluxservice_extension\Plugin\Rules\Action;

use Drupal\fluxservice_extension\Rules\FluxRulesPluginHandlerBaseExtended;

/**
 * fetch reference by service id.
 */
class fetchReferenceByRemoteEntity extends FluxRulesPluginHandlerBaseExtended implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxservice_fetch_reference_by_remote_id',
      'label' => t('Fetch reference by remote id'),
      'parameter' => array(
        'remote_id' => array(
          'type' => '*',
          'label' => t('Remote entity id'),
          'required' => TRUE,
          'wrapped' => FALSE,
        ),
        'remote_entity' => array(
          'type' => 'entity',
          'label' => t('Remote entity which contains the id'),
          'required' => TRUE,
          'wrapped' => FALSE,
        ),
        'local_type' => array(
          'type' => 'text',
          'label' => t('Local entity type'),
          'options list' => 'rules_entity_action_type_options',
          'description' => t('Specifies the type of referenced entity.'),
          'restriction' => 'input',
        ),
      ),
      'provides' => array(
        'reference' => array(
          'type'=>'entity',
          'label' => t('Fetched reference')),
      )
    );
  }

  /**
   * Executes the action.
   */
  public function execute($remote_id, $remote_entity, $local_type) {
//    print_r("<br>fetch reference: ".$remote_id."<br>");

    $module_name=explode('_', $remote_entity->entityType());


    $res=db_select($module_name[0],'fm')
          ->fields('fm',array('id','type','remote_type','remote_id'))
          ->condition('fm.remote_id',$remote_id,'=')
          ->condition('fm.type',$local_type,'=')
          ->execute()
          ->fetchAssoc();
    
    if($res){
      $reference=entity_load_single($res['type'], $res['id']);  
    }
    else{
      $res['type']=$remote_entity->entityType();

      $reference=$remote_entity;
    }

    return array('reference' => entity_metadata_wrapper($res['type'],$reference));
  }
}
