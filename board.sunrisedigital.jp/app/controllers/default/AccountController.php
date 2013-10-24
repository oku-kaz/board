<?php

/**
 *
 *
 * @author  Kazuya Okusaku <okusaku.sd@gmail.com>
 * @create  2013/10/23
 * @copyright 2013 Sunrise Digital Corporation.
 * @version  v 1.0 2013/10/23 17:25:13 Kazuya Okusaku
 **/
class AccountController extends Sdx_Controller_Action_Http
{
  public function createAction()
  {
    $form = new Sdx_Form();
    $form
            ->setActionCurrentPage() //アクション先を現在のURLに設定
            ->setMethodToPost();  //メソッドをポストに変更
    
    //各エレメントをフォームにセット
    //login_id
    $t_account = Bd_Orm_Main_Account::createTable();
    $elem = new Sdx_Form_Element_Text();
    $elem
            ->setName('login_id')
            ->addValidator(new Sdx_Validate_NotEmpty())
            ->addValidator(new Sdx_Validate_Regexp(
                    '/^[a-zA-Z0-9@_\-]+$/u',
                    '英数字と@_-のみ使用可能です。'
                    ))
            ->addValidator(new Sdx_Validate_Db_Unique(
                    $t_account,
                    'login_id',
                    $t_account->getSelect()->forUpdate()
                    ));
    $form->setElement($elem);
    
    //password
    $elem = new Sdx_Form_Element_Password();
    $elem
            ->setName('password')
            ->addValidator(new Sdx_Validate_NotEmpty())
            ->addValidator(new Sdx_Validate_StringLength(array('max'=>4)))
            ;
    $form->setElement($elem);
    
    //name
    $elem = new Sdx_Form_Element_Text();
    $elem
            ->setName('name')
            ->addValidator(new Sdx_Validate_NotEmpty())
            ->addValidator(new Sdx_Validate_StringLength(array('max'=>18)))
            ;
    $form->setElement($elem);
    
    if($this->_getParam('submit'))
    {
      $form->bind($this->_getAllParams());
      
      $account = new Bd_Orm_Main_Account();
      if($form->execValidate())
      {
        
      }
    }
    
    $this->view->assign('form', $form);
  }
}