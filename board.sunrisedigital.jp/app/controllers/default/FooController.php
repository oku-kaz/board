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
}