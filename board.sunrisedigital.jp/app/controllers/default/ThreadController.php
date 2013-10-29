<?php

define('THREAD_LIMIT' , 5);
define('ENTRY_LIMIT' , 10);

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
 
  public function indexAction()
  {
    Sdx_Debug::dump($this->_getParam('thread_id'), 'testdata');
  }
  
  public function listAction()
  {
    $thread_page = $this->_getParam('page') ? $this->_getParam('page') : 1;
    
    $t_account = Bd_Orm_Main_Account::createTable();
    $t_entry = Bd_Orm_Main_Entry::createTable();
    $t_thread = Bd_Orm_Main_Thread::createTable();
    
    //JOIN
    //$t_thread->addJoinLeft($t_entry);
    //$t_entry->addJoinLeft($t_account);
    
    //selectを生成
    $select = $t_thread->getSelectWithJoin();
    $select->order('id DESC');
    
    $thread_count = $t_thread->count($select);
//    Sdx_Debug::dump($thread_count, '$thread_count ');
    
    $pager_obj = new Sdx_Pager(THREAD_LIMIT, $thread_count);
    $pager_obj->setPage($thread_page);
    
/*    $sdx_context = Sdx_Context::getInstance();
    Sdx_Debug::dump($sdx_context->getVar('signed_account')->getId(), '$Id ');
    */
    $select->limit(THREAD_LIMIT, ($thread_page - 1) * THREAD_LIMIT);
    
//    $t_thread->appendJoin($select);
    //$listはSdx_Db_Record_Listのインスタンス
    $list = $t_thread->fetchAll($select);
    
/*    
    unset($select);
    
    $t_entry->addJoinLeft($t_account);
    foreach($list as $thread_key => $thread_current){
      $select = $t_entry->getSelectWithJoin();
      $select->where('thread_id = ?' ,$thread_current->getId());
      $select->order('id DESC');
      $select->limit(3, 0);
      $entry_list = $t_entry->fetchAll($select);
//      $list[$thread_key]['getEntry'] = $entry_list;
    }
 * 
 */
    
    //テンプレートにレコードリストのままアサイン
    $this->view->assign('thread_list', $list);
    
    $this->view->assign('t_entry', $t_entry);
    
    $this->view->assign('current_page', $thread_page);
    $this->view->assign('max_page', ceil($thread_count / THREAD_LIMIT));
    
    $this->view->assign('prev_page', $pager_obj->getPrevPageId());
    $this->view->assign('next_page', $pager_obj->getNextPageId());
    
    $this->view->assign('last_page', $pager_obj->getLastPageId());
    
  }
  
  public function mainAction()
  {
    
    $page = $this->_getParam('page') ? $this->_getParam('page') : 1;
    $thread_id = $this->_getParam('thread_id') ? $this->_getParam('thread_id') : 1;

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
    
    if($this->_getParam('submit'))
    {
      $sdx_context = Sdx_Context::getInstance();
      $form->bind($this->_getAllParams());
      
      $entry = new Bd_Orm_Main_Entry();
      $db = $entry->updateConnection();
      
      $db->beginTransaction();
      try {
        //Validateの実行はFOR UPDATEのためトランザクションの内側
        if($form->execValidate())
        {
          $entry
                  ->setThreadId($this->_getParam('thread_id'))
                  ->setAccountId($sdx_context->getVar('signed_account')->getId())
                  ->setBody($this->_getParam('body'))
                  ;
                  
          $entry->save();
          $db->commit();
          $this->redirectAfterSave('/thread/main'.$thread_id.'/'.$page);
          
        }
        else
        {
          $db->rollback();
        }
        
      } catch (Exception $ex) {
        $db->rollback();
        throw $ex;
      }
      
    }
    
    $this->view->assign('form', $form);

    
    
    
    //selectを生成
    $select = $t_thread->getSelectWithJoin();
    $select->where('id = ?' ,$thread_id);
    $thread = $t_thread->fetchAll($select);
    
    unset($select);
    
    $select = $t_entry->getSelectWithJoin();
    $select->where('thread_id = ?' ,$thread_id);
    $select->order('id DESC');
    
    $count = $t_entry->count($select);
    
    $select->limit(ENTRY_LIMIT, ($page - 1) * ENTRY_LIMIT);
    
    $pager_obj = new Sdx_Pager(ENTRY_LIMIT, $count);
    $pager_obj->setPage($page);
    
    $select->limit(ENTRY_LIMIT, ($page - 1) * ENTRY_LIMIT);
    
    //$listはSdx_Db_Record_Listのインスタンス
    $list = $t_entry->fetchAll($select);
    
    $this->view->assign('thread_id', $thread_id);
    $this->view->assign('thread', $thread);
    
    $this->view->assign('entry_list', $list);
    
    $this->view->assign('current_page', $page);
    $this->view->assign('max_page', ceil($count / THREAD_LIMIT));
    
    $this->view->assign('prev_page', $pager_obj->getPrevPageId());
    $this->view->assign('next_page', $pager_obj->getNextPageId());
    
    $this->view->assign('last_page', $pager_obj->getLastPageId());
    
  }

}