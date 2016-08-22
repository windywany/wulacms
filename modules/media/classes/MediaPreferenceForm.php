<?php
class MediaPreferenceForm extends AbstractForm {
	private $upload_dir = array ('group' => '1','col' => '3','label' => '存储路径','default' => '','note' => '不填写时使用默认值：uploads，当接入多媒体服务器时此设置无效.' );
	private $store_dir = array ('group' => '1','col' => '6','label' => '存储目录','widget' => 'radio','default' => '/Y/n/','defaults' => "/Y/=按年\n/Y/n/=按月\n/Y/n/j/=按日",'note' => '不填写时使用默认值：按月存储，当接入多媒体服务器时此设置无效.' );
	private $rand_cnt = array ('group' => '1','col' => '3','label' => '分组数','default' => '0','note' => '将文件随机分配到设定的组内存储','rules' => array ('digits' => '必须是数字' ) );
	private $max_upload_size = array ('group' => '2','col' => '3','label' => '最大上传文件大小(单位M)','default' => '20','rules' => array ('required' => '请填写文件大小','digits' => '请填写正确的文件大小' ) );
	private $allow_exts = array ('group' => '2','col' => '9','label' => '允许文件类型','default' => 'jpg,gif,png,bmp,jpeg,zip,rar,7z,tar,gz,bz2,doc,docx,txt,ppt,pptx,xls,xlsx,pdf,mp3,avi,mp4,flv,swf','note' => '请填写允许的文件类型的扩展名,多个类型以逗号分隔。' );
	private $enable_watermark = array ('group' => '2_5','col' => '3','label' => '添加水印','widget' => 'radio','default' => '0','defaults' => "0=不添加\n1=添加" );
	private $transxy = array ('group' => '2_5','col' => '3','label' => '随机偏移','default' => '150x150','note' => '格式为:水平x垂直(如200x200)','rules' => array ('regexp(/^[\d]+x[\d]+$/)' => '格式不正确.' ) );
	private $watermark_min_size = array ('group' => '2_5','col' => '6','label' => '图片小于以下尺寸时不添加水印','default' => '200x200','note' => '格式为:宽度x高度(如200x200)','rules' => array ('regexp(/^[\d]+x[\d]+$/)' => '格式不正确.' ) );
	private $watermark_pos = array ('group' => '3','col' => '3','label' => '水印位置','widget' => 'select','default' => 'rd','defaults' => "rd=随机\ntl=左上(tl)\ntm=上中(tm)\ntr=右上(tr)\nml=左中(ml)\nmm=居中(mm)\nmr=右中(mr)\nbl=右下(bl)\nbm=下中(bm)\nbr=右下(br)" );
	private $watermark = array ('group' => '3','col' => '9','label' => '水印图片','widget' => 'image','rules' => array ('required(enable_watermark_1:checked:1)' => '请上传水印图片' ),'defaults' => '{"water":0,"locale":1}' );
	private $title_alt = array ('label' => '将文章标题做为图片的ALT输出','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
	private $allow_remote = array ('label' => '作为多媒体服务器','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是",'note' => '作为多媒体服务器后其它应用可以接入.当接入多媒体服务器时此设置无效.' );
}