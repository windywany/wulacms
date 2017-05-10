<?php
/**
 * 列表视图基类.
 * @author 宁广丰.
 *
 */
abstract class MobiListView implements Renderable {
	
	/**
	 * 取系统支持的列表视图.
	 *
	 * @return array (key=>array('name'=>'name','clz'=>MobiListViewInstance)
	 */
	public static function getListViews() {
		static $views = false;
		if ($views === false) {
			$_views = apply_filter ( 'get_mobi_list_views', array (new PicTextCellView (),new ThreePicCellView (),new OnePicCellView (),new CursouelLlistView () ,new \mobiapp\classes\GifCellView(),new \mobiapp\classes\VideoCellView()) );
			$views = array ();
			foreach ( $_views as $view ) {
				if ($view instanceof MobiListView) {
					$id = $view->getId ();
					$name = $view->getName ();
					$views [$id] = array ('name' => $name,'clz' => $view );
				}
			}
		}
		return $views;
	}
	/**
	 * 是否是轮播图.
	 */
	public function isCarousel() {
		return 0;
	}
	/**
	 * 取ID.
	 *
	 * @return string ID.
	 */
	public abstract function getId();
	/**
	 * 布局样式名称.
	 *
	 * @return string 名称.
	 */
	public abstract function getName();
	/**
	 * 填充编辑表单.
	 *
	 * @param AbstractForm $form        	
	 */
	public abstract function fillEditForm(&$form);
	/**
	 * 取此表单中的数据数组.
	 *
	 * @param array $data        	
	 */
	public abstract function getCustomData($data);
	/**
	 * 填充供list view 使用的数据.
	 *
	 * @param array $data
	 *        	common data.
	 * @param array $customData
	 *        	自定义数据.
	 */
	public abstract function fillListViewData(&$data, $customData);
}
