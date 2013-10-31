{extends file='default/base.tpl'}
{block title append}board{/block}
{block main_contents}
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">board</h3>
    </div>
    <div class="panel-body">
      <a href="/thread/list" class="list-group-item">スレッド一覧</a>
      <a href="/account/create" class="list-group-item">新規アカウント作成</a>
    </div>
    <div class="panel-body">
      <a href="/control/thread" class="list-group-item">新規スレッド作成</a>
      <a href="/control/genre" class="list-group-item">ジャンル編集</a>
    </div>
    <div class="panel-body">
      <a href="/account/list" class="list-group-item">アカウント一覧</a>
    </div>
  </div>
{/block}