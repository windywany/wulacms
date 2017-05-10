<?php
class AppVerMarketForm extends AbstractForm {
	private $id = array ('widget' => 'hidden','rules' => array ('digits' => '编号只能是数字.','regexp(/^[1-9][\d]*$/)' => '编号只能是数字,最大长度为10' ) );
	private $version_id = array ('widget' => 'hidden','rules' => array ('digits' => '编号只能是数字.','regexp(/^[1-9][\d]*$/)' => '版本编号只能是数字,最大长度为10' ) );
	private $ad_config_id = array ('group' => 1,'col' => 6,'label' => '广告配置方案','widget' => 'auto','defaults' => 'app_ads,id,name,pst:system/preference' );
	
	protected function init_form_fields($data, $value_set) {
		if ($data ['url']) {
			$this->addField ( 'url', array ('label' => '下载路径','group' => 1,'col' => 6 ) );
		}
		// 如果是新增则是批量升级，如果是修改则是单个修改
		if ($data ['id']) {
		    $this->addField ( 'market_name', array ('label' => '渠道名称','group' => 2,'col' => 6,'rules' => array ('required' => '请填写应用市场名称.') ) );
			$this->addField ( 'market', array ('label' => '渠道标识','group' => 2,'col' => 6,'rules' => array ('required' => '请填写应用市场.','regexp(/^[a-z0-9_]*$/i)' => '应用市场只能是字母,数字和下划线的组合.','callback(@checkMarket,version_id,id)' => '市场已经存在' ) ) );
		} else {
			$this->addField ( 'market', array ('label' => '渠道','group' => 1,'col' => 6,'widget' => 'textarea','note' => "每个渠道一行，渠道标示|渠道名称",'rules' => array ('required' => '请填写应用市场.','callback(@checkMarket,version_id,id)' => '市场已经存在' ) ) );
		}
	}
	
	/**
	 * 单个市场检查
	 *
	 * @author DQ
	 *         @date 2015年12月3日 下午4:21:50
	 * @param        	
	 *
	 * @return
	 *
	 */
	public function checkMarket($value, $data, $message) {
		if ($data ['version_id'] && $value) {
		    //单个编辑
		    if($data['id']){
		        $where = array ('version_id' => $data ['version_id'],'market' => $value );
		        if ($data ['id']) {
		            $where = array_merge ( $where, array ('id <>' => $data ['id'] ) );
		        }
		        $db = dbselect ( '*' );
		        $exist = $db->from ( 'app_version_market' )->where ( $where )->get ();
		        if ($exist) {
		            return $message;
		        }
		        return true;
		    }else{
		        //批量新增
		        $rsMarket = array_filter ( explode ( "\n", trim ( $value ) ) );
		        foreach ( $rsMarket as $val ) {
		            $rsTemp = explode('|', $val);
		            $rsTemp[1] = !$rsTemp[1]?$rsTemp[0]:$rsTemp[1];
		            $where = array ('version_id'=>$data ['version_id'],'market' => $rsTemp[0],'deleted' => 0 );
		            $db = dbselect ( '*' );
		            $exist = $db->from ( 'app_version_market' )->where ( $where )->get ();
		            if ($exist) {
		                return $rsTemp[0] . ' ' . $message;
		            }
		        }
		        return true;
		    }
		}
		return false;
	}
	
	
	
}
