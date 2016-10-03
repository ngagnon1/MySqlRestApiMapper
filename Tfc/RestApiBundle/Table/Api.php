<?php

namespace Tfc\RestApiBundle\Table;

use Tfc\BaseBundle\Core;

class Api extends Core\TableClass {

  public static function getSetObjSet(){
    $class = static::getTableClassName("Obj");
    $ret = $class::getSet(array(
      "Limit" => 100,
    ));
    return $ret;
  }

  public static function getSingleApiDefinition(){
    $name = static::getTable()->getName();
    $members = static::staticOption("Api.GetSingle.Members");
    $class = static::getTableClassName("Obj");
    $arr = array();
    foreach( $members as $member ){
      if( $member_op = $class::memberOptions( $member ) ){
        $member_class = $member_op["Class"];
        $member_api_class = $member_class::getTableClassName("Api");
        $opk = $member_api_class::registerStaticOption( "Api.CallerClass", $class );
        if( $member_op["IsArray"] ){
          $member_def = $member_api_class::staticOption("Api.GetSet.Definition");
        }
        else{
          $member_def = $member_api_class::staticOption("Api.GetSingle.Definition");
        }
        $member_api_class::unregisterStaticOption($opk);
        $arr[$member] = $member_def;
      }
      else{
        $arr[] = $member;
      }
    }

    return $arr;
  }

  public static function getSetApiDefinition(){
    return array( static::getSingleApiDefinition() );
  }

  public static function getMembers(){

    $ret = array();

    $obj_name = static::getTableClassName("Obj");
    $fields = $obj_name::fieldOptions();
    $id_field = static::getTable()->getIdField();
    foreach( $fields as $f ){
      if( 
        ( !preg_match( "/Id$/", $f["Field"] ) || $f["Key"] == 'PRI' ) 
        && $f["Field"] <> "DateTimeStamp"
      ){
        $ret[] = $f["Field"];
      }
    }
    if( !static::staticOption( "Api.CallerClass" ) ){
      $members = $obj_name::memberOptions();
      foreach( $members as $m ){
        if( 
          $m["LocalField"] <> $id_field["Field"] 
          || $m["IsArray"]
        ){
          $ret[] = $m["Name"];
        }
      }
    }

    return $ret;
  }

}



