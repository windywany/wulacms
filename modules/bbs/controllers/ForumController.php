<?php
namespace bbs\controllers;

class ForumController extends \Controller {

    protected $acls = [ '*' => 'r:bbs/forum','save' => 'id|u:bbs/forum;c:bbs/forum','add' => 'c:bbs/forum','del' => 'd:bbs/forum','csort' => 'u:cms/page' ];

    protected $checkUser = true;

    public function index() {
        $data ['items'] = dbselect ( 'name,upid,id' )->from ( '{bbs_forums}' )
            ->where ( [ 'upid' => 0,'deleted' => 0 ] )
            ->asc ( 'sort' );
        $data ['cnt'] = dbselect ()->from ( '{bbs_forums}' )
            ->where ( [ 'deleted' => 0 ] )
            ->count ( 'id' );
        $data ['search'] = false;
        return view ( 'forum/index.tpl', $data );
    }

    public function data($_tid = 0) {
        $_tid = intval ( $_tid );
        if ($_tid > 0) {
            $data ['items'] = dbselect ( 'name,upid,id' )->from ( '{bbs_forums}' )
                ->where ( [ 'upid' => $_tid,'deleted' => 0 ] )
                ->asc ( 'sort' );
        } else {
            $data ['items'] = [ ];
        }
        return view ( 'forum/index.tpl', $data );
    }

    public function add() {
        return view ( 'forum/form.tpl', [ ] );
    }
}