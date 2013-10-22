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
  public function indexAction()
  {
    Sdx_Debug::dump($this->_getParam('thread_id'), 'testdata');
  }
}