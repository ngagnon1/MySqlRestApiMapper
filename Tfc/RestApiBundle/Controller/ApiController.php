<?php

namespace Tfc\RestApiBundle\Controller;

use Tfc\BaseBundle\Core;
use Tfc\BaseBundle\Table\Obj;

use FOS\RestBundle\Controller\FOSRestController;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ApiController extends FOSRestController {

  public function allAction( $type, $cmd=NULL ){

    $target = Core\Util::camelCase($type);
    $tbl = Core\Table::name2Table($target);
    $class_name = $tbl->getClassName();
    $api_class =  $class_name::getTableClassName("Api");
    $objs = $api_class::get('Api.GetSet.ObjSet');
    $def = $api_class::get('Api.GetSet.Definition');

    $data = array();
    $data["{$target}Set"] = Core\Util::def2Array($def,$objs);
    $view = $this
      ->view($data, 200)
      ->setFormat('json');

    return $this->handleView($view);

  }

  public function getAction( $type, $id=NULL ){

    $target = Core\Util::camelCase($type);
    $tbl = Core\Table::name2Table($target);
    $class_name = $tbl->getClassName();
    $api_class =  $class_name::getTableClassName("Api");
    $obj = $class_name::get($id);
    if( !$obj->getId() )
      throw new NotFoundHttpException("$target not found");

    $def = $api_class::staticOption('Api.GetSingle.Definition');

    $data = array();
    $data["$target"] = Core\Util::def2Array($def,$obj);
    $view = $this
      ->view($data, 200)
      ->setFormat('json');

    return $this->handleView($view);

  }

  public function newAction(Request $request, $type ){

    $target = Core\Util::camelCase($type);
    $tbl = Core\Table::name2Table($target);
    $class_name = $tbl->getClassName();
    $api_class = $class_name::getTableClassName("Api");
    $form_class =  $class_name::getTableClassName("Form");
    $form_obj = new $form_class;
    $target_obj = new $class_name;
    $form_obj->setTarget($target_obj);
    $form = $form_obj->getCreateForm();

    $form->submit($request->get($target));

    if( $form->isValid() ){
      $target_obj->Insert();
      $data = array( $target => $target_obj->getFieldArray() );
      return $this->handleView( $this
        ->view($data, 200 )
        ->setFormat('json') );
    }

    return $this->handleView( $this
        ->view($form, 400 )
        ->setFormat('json') );

  }

  public function updateAction(Request $request, $type, $id ){

    $target = Core\Util::camelCase($type);
    $tbl = Core\Table::name2Table($target);
    $class_name = $tbl->getClassName();
    $api_class = $class_name::getTableClassName("Api");
    $form_class =  $class_name::getTableClassName("Form");
    $form_obj = new $form_class;
    $target_obj = $class_name::get($id);
    $form_obj->setTarget($target_obj);
    $form = $form_obj->getEditForm();

    $form->submit($request->get($target));

    if( $form->isValid() ){
      $target_obj->Update();
      $data = array( $target => $target_obj->getFieldArray() );
      return $this->handleView( $this
        ->view($data, 200 )
        ->setFormat('json') );
    }

    return $this->handleView( $this
        ->view($form, 400 )
        ->setFormat('json') );

  }

  public function deleteAction(Request $request, $type, $id ){

    $target = Core\Util::camelCase($type);
    $tbl = Core\Table::name2Table($target);
    $class_name = $tbl->getClassName();
    $api_class = $class_name::getTableClassName("Api");
    $form_class =  $class_name::getTableClassName("Form");
    $form_obj = new $form_class;
    $target_obj = $class_name::get($id);

    if( $target_obj->getId() ){
      $target_obj->delete();
      return $this->handleView( $this
          ->view(["deleted"=>true], 200 )
          ->setFormat('json') );
    }
    else
      return $this->handleView( $this
          ->view($form, 400 )
          ->setFormat('json') );
  }

}


