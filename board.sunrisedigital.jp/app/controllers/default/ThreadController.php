<?php

/**
 *
 *
 * @author  Kazuya Okusaku <okusaku.sd@gmail.com>
 * @create  2013/10/22
 * @copyright 2013 Sunrise Digital Corporation.
 * @version  v 1.0 2013/10/22 16:40:54 Kazuya Okusaku
 **/
class ThreadController extends Sdx_Controller_Action_Http
{
  
  const THREAD_LIMIT = 5;
  const ENTRY_LIMIT = 10;
 
  public function indexAction()
  {
    Sdx_Debug::dump($this->_getParam('thread_id'), 'testdata');
  }
  
  public function listAction()
  {
    $page = $this->_getParam('page', 1);
    
    $t_account = Bd_Orm_Main_Account::createTable();
    $t_entry = Bd_Orm_Main_Entry::createTable();
    $t_thread = Bd_Orm_Main_Thread::createTable();
    
    //selectを生成
    $select = $t_thread->getSelectWithJoin();
    $select->order('id DESC');
    
    $thread_count = $t_thread->count($select);
    $pager_obj = new Sdx_Pager(self::THREAD_LIMIT, $thread_count);
    $pager_obj->setPage($page);
    
    $select->limitPager($pager_obj);
    
    //$listはSdx_Db_Record_Listのインスタンス
    $list = $t_thread->fetchAll($select);
    
    //テンプレートにレコードリストのままアサイン
    $this->view->assign('thread_list', $list);
    //テンプレートでselect条件変更するためオブジェクト渡し
    $this->view->assign('t_entry', $t_entry);
    //ページャ用
    $this->view->assign('pager', $pager_obj);
  }
  
  public function mainAction()
  {
    $page = $this->_getParam('page', 1);
    if(!$thread_id = $this->_getParam('thread_id')){
      //スレッドが不明な場合リストページへ
      $this->redirect('/thread/list');
    }

    $t_account = Bd_Orm_Main_Account::createTable();
    $t_entry = Bd_Orm_Main_Entry::createTable();
    $t_thread = Bd_Orm_Main_Thread::createTable();

    //JOIN
    $t_account->addJoinLeft($t_entry);
    $t_entry->addJoinLeft($t_thread);
 
    $form = new Sdx_Form();
    $form
            ->setActionCurrentPage() //アクション先を現在のURLに設定
            ->setMethodToPost();  //メソッドをポストに変更
    //各エレメントをフォームにセット
    //body
    $elem = new Sdx_Form_Element_Text();
    $elem
            ->setName('body')
            ->addValidator(new Sdx_Validate_NotEmpty())
            ->addValidator(new Sdx_Validate_StringLength(array('max'=>255)))
            ;
    $form->setElement($elem);
    
    $sdx_context = Sdx_Context::getInstance();
    //submit時
    if($this->_getParam('submit'))
    {
      if(!($sdx_context->getVar('signed_account'))){
        //ログインページへ
        $this->redirect('/secure/login');
      }
      
      $form->bind($this->_getAllParams());
      
      $entry = new Bd_Orm_Main_Entry();
      $db = $entry->updateConnection();
      
      if($form->execValidate()){
        $db->beginTransaction();
        try {
          $entry
                  ->setThreadId($this->_getParam('thread_id'))
                  ->setAccountId($sdx_context->getVar('signed_account')->getId())
                  ->setBody($this->_getParam('body'))
                  ;
          $entry->save();
          $db->commit();
          $this->redirectAfterSave('/thread/main/'.$thread_id.'/'.$page);
        } catch (Exception $ex) {
          $db->rollback();
          throw $ex;
        }
      }
    }
    
    $this->view->assign('form', $form);

    //selectを生成
    $select = $t_thread->getSelectWithJoin();
    $thread = $t_thread->fetchByPkeys(array('id'=>$thread_id));
    /*
    $select->where('id = ?' ,$thread_id);
    $thread = $t_thread->fetchAll($select);
     * 
     */
    
    unset($select);
    
    $select = $t_entry->getSelectWithJoin();
    $select->where('thread_id = ?' ,$thread_id);
    $select->order('id DESC');
    
    $count = $t_entry->count($select);
    $pager_obj = new Sdx_Pager(self::ENTRY_LIMIT, $count);
    $pager_obj->setPage($page);
    
    $select->limitPager($pager_obj);
    
    //$listはSdx_Db_Record_Listのインスタンス
    $list = $t_entry->fetchAll($select);
    
    $this->view->assign('thread', $thread[0]);
    //テンプレートにレコードリストのままアサイン
    $this->view->assign('entry_list', $list);
    //ページャ用
    $this->view->assign('pager', $pager_obj);

  }

}