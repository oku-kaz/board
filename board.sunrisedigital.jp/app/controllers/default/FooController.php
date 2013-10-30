<?php
/**
 *
 *
 * @author  Kazuya Okusaku <okusaku.sd@gmail.com>
 * @create  2013/10/22
 * @copyright 2013 Sunrise Digital Corporation.
 * @version  v 1.0 2013/10/22 16:39:23 Kazuya Okusaku
 **/
class FooController extends Sdx_Controller_Action_Http
{
  public function indexAction() {
    $this->_disableViewRenderer();
  }
  public function barAction() {
    $this->view->assign('date', date('Y-m-d H:i:s'));
  }
  public function dbAction() {
    $this->_disableViewRenderer();
    
    //接続を取得
    $db = Bd_Db::getConnection('board_master');
    
    //トランザクション開始
    $db->beginTransaction();
    
    //テーブル名を指定してINSERT文を生成・実行
    $db->insert('account',array(
        'login_id' => 'admin',
        'password' => 'some_password',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ));
    
    //コミット
    $db->commit();
    
    //取得して確認
    Sdx_Debug::dump($db->query("SELECT * FROM account")->fetchAll(), 'title');
  }
  public function ormNewAction()
  {
    $this->_disableViewRenderer();
    
    //レコードクラスの生成
    $account = new Bd_Orm_Main_Account();
    
    $account
            ->setLoginId('test')
            ->setPassword('flkdjf0')
            ;
    
    //このレコードが使用する接続を取得
    $db = $account->updateConnection();
    
    $db->beginTransaction();
    $account->save();
    $db->commit();
    
    //取得して確認
    Sdx_Debug::dump($db->query("SELECT * FROM account")->fetchAll(), 'title');
  }
  public function ormSelectAction()
  {
    $this->_disableViewRenderer();
    
    //テーブルクラスの取得
    $t_account = Bd_Orm_Main_Account::createTable();
    
    //簡単なJOIN
    //JOIN対象のテーブルをすべて生成
    $t_account = Bd_Orm_Main_Account::createTable();
    $t_entry = Bd_Orm_Main_Entry::createTable();
    
    //INNER JOIN
    $t_account->addJoinInner($t_entry);
    
    //selectできるメソッドはgetSelectWithJoinだけ
    $select = $t_account->getSelectWithJoin();
    
    $list = $t_account->fetchAll($select);
/*    
    //主キー1のレコードを取得
    $account = $t_account->findByPkey(3);
    
    //toArray()はレコードの配列表現を取得するメソッドです。
    Sdx_Debug::dump($account->toArray(), 'select');
*/
/*    
    //Selectの取得
    $select = $t_account->getSelect();
    
    //selectにWHERE句を追加
    $select->add('id', array(1, 3));
    
    //SQLを発行
    $list = $t_account->fetchAll($select);
    
    //fetchAllの返り値は配列ではなくBd_Db_Recode_Listのインスタンスです
    Sdx_Debug::dump($list, 'fetchall_dump');
*/ 
    //Recode_Listの配列表現をdump
    Sdx_Debug::dump($list->toArray(), 'fetchall_array');
   
  }
  public function ormUpdateAction()
  {
    $this->_disableViewRenderer();
    
    //テーブルクラスの取得
    $t_account = Bd_Orm_Main_Account::createTable();
    //主キー1のレコードを取得
    $account = $t_account->findByPkey(1);
    
    $account->setPassword('updated_password_'.date('Y-m-d H:i:s'));
    
    $db = $account->updateConnection();
    
    $db->beginTransaction();
    $account->save();
    $db->commit();
    
    //取得して確認
    Sdx_Debug::dump($db->query("SELECT * FROM account")->fetchAll(), 'select_fetch');
  }
  public function ormDeleteAction()
  {
    $this->_disableViewRenderer();
    
    //テーブルクラスの取得
    $t_account = Bd_Orm_Main_Account::createTable();
    //主キー1のレコードを取得
    $account = $t_account->findByPkey(8);
    
    $db = $account->updateConnection();
    
    $db->beginTransaction();
    $account->delete();
    $db->commit();
    
    //取得して確認
    Sdx_Debug::dump($db->query("SELECT * FROM account")->fetchAll(), 'select_fetch');
  }
}