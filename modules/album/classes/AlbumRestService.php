<?php
class AlbumRestService {
	/**
	 * 注册.
	 *
	 * @param RestServer $server        	
	 */
	public static function on_init_rest_server($server) {
		$server->registerClass ( new AlbumRestService (), '1', 'album' );
		return $server;
	}
	/**
	 * 首页slide API.
	 *
	 * @param array $param
	 *        	无.
	 * @param string $appkey        	
	 * @param string $sceret        	
	 * @return 结果集：{count:2,media:'http://aaa.com/',items[{url,albumid,name}]}
	 */
	public function rest_home_slide($param, $appkey, $sceret) {
		$data = get_data_from_cts_provider ( 'block', array ('limit' => '0,5','refid' => 'home_slide','sortby' => 'sort','order' => 'a' ), array () );
		$slider = array ('count' => $data->count (),'media' => the_media_src ( '' ) );
		foreach ( $data as $slide ) {
			$s ['url'] = $slide ['image'];
			$s ['albumid'] = $slide ['page_id'];
			$s ['name'] = $slide ['page_title'];
			$slider ['items'] [] = $s;
		}
		return $slider;
	}
	/**
	 * 首页三个推荐相册.
	 *
	 * @param array $param
	 *        	none.
	 * @param string $appkey
	 *        	.
	 * @param string $sceret
	 *        	.
	 * @return 结果集：{count:2,media:'http://aaa.com/',items[{url,albumid,name}]}
	 */
	public function rest_recommand_album($param, $appkey, $sceret) {
		$data = get_data_from_cts_provider ( 'pages', array ('model' => 'album','subch' => 'on','flags' => 'c','sortby' => 'update_time','order' => 'd','limit' => '0,3','pp' => 'off' ), array () );
		
		$albums = array ('count' => count ( $data ),'media' => the_media_src ( '' ) );
		foreach ( $data as $album ) {
			$a ['name'] = $album ['title'];
			$a ['url'] = $album ['image'];
			$a ['albumid'] = $album ['id'];
			$albums ['items'] [] = $a;
		}
		return $albums;
	}
	/**
	 * 相册列表数据.
	 *
	 * @param array $param
	 *        	{page:1}.
	 * @param string $appkey
	 *        	.
	 * @param string $sceret
	 *        	.
	 * @return {more:true|false,count:20,media:'',items:[{url,name,albumid,cnt}]}.
	 */
	public function rest_album_list($param, $appkey, $sceret) {
		if (isset ( $param ['page'] )) {
			$page = intval ( $param ['page'] );
			if ($page <= 0) {
				$page = 1;
			}
		} else {
			$page = 1;
		}
		$rows = dbselect ( 'CP.id,CP.title,CP.image' )->from ( '{cms_page} AS CP' );
		$cnt = dbselect ( imv ( 'COUNT(AI.id)' ) )->from ( '{album_item} AS AI' )->where ( array ('AI.album_id' => imv ( 'CP.id' ),'AI.deleted' => 0,'AI.width >' => 0 ) );
		$rows->field ( $cnt, 'album_cnt' );
		$where ['CP.deleted'] = 0;
		$where ['CP.hidden'] = 0;
		$pid = irqst ( 'pid' );
		$where ['CP.model'] = 'album';
		
		$rows->where ( $where );
		$rows->sort ( 'CP.flag_c', 'd' );
		$rows->sort ( 'CP.update_time', 'd' );
		$rows->limit ( ($page - 1) * 20, 20 );
		$cnt = $rows->count ( 'CP.id' );
		if ($cnt < $page * 20) {
			$data ['more'] = false;
		} else {
			$data ['more'] = true;
		}
		$data ['last_sync_time'] = time ();
		$data ['media'] = the_media_src ( '' );
		foreach ( $rows as $album ) {
			$a ['url'] = $album ['image'];
			$a ['name'] = $album ['title'];
			$a ['albumid'] = $album ['id'];
			$a ['cnt'] = $album ['album_cnt'];
			$data ['items'] [] = $a;
		}
		$data ['count'] = count ( $data ['items'] );
		return $data;
	}
	/**
	 * 图片列表.
	 * 此接口可以提供三种参数：
	 * 1. hot 是否是推荐
	 * 2. albumid 哪个相册的图片.
	 * 3. page 第几页
	 *
	 * 其中albumid 和 hot 任选一。
	 *
	 * @param array $param
	 *        	array('page'=>1,'albumid'=>0).
	 * @param string $appkey
	 *        	.
	 * @param string $sceret
	 *        	.
	 * @return {more:true,count:5,media:'',items:[{url,name,albumid,album}]}.
	 *
	 */
	public function rest_pic_list($param, $appkey, $sceret) {
		if (isset ( $param ['page'] )) {
			$page = intval ( $param ['page'] );
			if ($page <= 0) {
				$page = 1;
			}
		} else {
			$page = 1;
		}
		$where = array ('AI.deleted' => 0,'AI.width >' => 0 );
		if (isset ( $param ['albumid'] )) {
			$where ['AI.album_id'] = intval ( $param ['albumid'] );
		} else {
			$where ['AI.is_hot'] = 1;
		}
		
		$rows = dbselect ( 'AI.url,AI.title,AI.width,AI.height,AI.album_id,CP.title AS album_name' )->from ( '{album_item} AS AI' )->join ( '{cms_page} AS CP', 'AI.album_id = CP.id' );
		
		$rows->where ( $where );
		$rows->sort ( 'AI.update_time', 'd' );
		$rows->limit ( ($page - 1) * 20, 20 );
		
		$cnt = $rows->count ( 'AI.id' );
		if ($cnt < $page * 20) {
			$data ['more'] = false;
		} else {
			$data ['more'] = true;
		}
		$data ['last_sync_time'] = time ();
		$data ['media'] = the_media_src ( '' );
		foreach ( $rows as $row ) {
			$pic ['url'] = $row ['url'];
			$pic ['title'] = $row ['title'];
			$pic ['albumid'] = $row ['album_id'];
			$pic ['name'] = $row ['album_name'];
			$pic ['width'] = $row ['width'];
			$pic ['height'] = $row ['height'];
			$data ['items'] [] = $pic;
		}
		$data ['count'] = count ( $data ['items'] );
		return $data;
	}
}
