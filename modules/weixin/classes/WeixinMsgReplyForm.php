<?php
 /**
    * 文本信息回复
    * @author DQ
    * @date 2015年10月12日 上午11:10:33
    * 
    */
class WeixinMsgReplyForm extends AbstractForm {
    private $id = array (
        'widget' => 'hidden',
        'rules' => array (
            'regexp(/^[1-9][\d]*$/)' => '错误的编号.'
        )
    );
    private $msg_type = array (
        'label' => '消息类型',
        'widget' => 'radio',
        'rules' => array (
            'required' => '请填写关键字.',
        ),
        'default'=>'text',
        'defaults' => array(
            'text' => '文本消息',
            'image' => '图片消息',
            'voice' => '语音消息',
            'video' => '视频消息',
            'shortvideo' => '小视频消息',
            'location' => '地理位置消息',
            'link' => '链接消息',
            'event' => '链接消息',
        ),
        'group' => '1',
        'col' => 12
    );
    private $reply_type = array (
        'label' => '回复消息类型',
        'widget' => 'radio',
        'rules' => array (
            'required' => '请填写关键字.',
        ),
        'default'=>'text',
        'defaults' => array(
            'text' => '回复文本消息',
            'image' => '回复图片消息',
            'voice' => '回复语音消息',
            'video' => '回复视频消息',
            'music' => '回复音乐消息',
            'news' => '回复图文消息'
        ),
        'group' => '2',
        'col' => 12
    );
    
    
    private $keyword = array (
        'label' => '关键字',
        'widget' => 'text',
        'rules' => array (
            'required' => '请填写关键字.',
            'callback(@checkKeyword,id)' => '关键字已经存在.'
        ),
        'group' => '6',
        'col' => 5
    );
    private $content = array (
        'label' => '回复内容',
        'widget' => 'textarea',
        'rules' => array (
            'required' => '请填写回复内容.',
        ),
        'group' => '7',
        'col' => 3
    );
    
     /**
        * 检测关键字是否存在
        * @author DQ
        * @date 2015年10月30日 下午4:10:04
        * @param
        * @return 
        * 
        */
    public function checkKeyword($value, $data, $message) {
        $rs = dbselect ( 'username' )->from ( '{lz_text_reply}' )->where (array ('id <>' => $data ['id'],'keyword' => trim($value)) )->get (0);
        return empty ( $rs ) ? true : $message;
    }
    
    
     /**
        * 注释
        * @author DQ
        * @date 2015年10月30日 下午4:38:38
        * @param
        * @return 
        * 
        */
    function replyContent($keyword=''){
        $keyword = trim($keyword);
        $msg ="关注“来赚”邀请更多好友可以让你更加富有，只需转发文章通过其他用户阅读后即可产生你的收益，还在等待什么，让我们一起转发吧！
微信号：laizhuancc
商务合作：QQ 66449
来赚-1群：&lt;a href=&quot;http://jq.qq.com/?_wv=1027&k=ZDcu2M&quot;&gt;329249452&lt;/a&gt;
客服：QQ 3351386413
初来咋到的你，可以戳戳下面的菜单了解来赚。

&lt;a href=&quot;http://share.chaosuwifi.com/laizhuan/help/xsjc&quot;&gt;点这里查看新手教程&lt;/a&gt;
当然，你疑惑的时候我也是在这里等你
&lt;a href=&quot;http://share.chaosuwifi.com/laizhuan/help/cjwt&quot;&gt;点击查看常见问题&lt;/a&gt;
您使用本平台服务即表示
已接收本平台的服务协议
&lt;a href=&quot;http://share.chaosuwifi.com/laizhuan/help/service&quot;&gt;点击查看服务协议详情&lt;/a&gt;";
        
        if(!$keyword){
            return $msg;
        }
        $rs = dbselect ( 'content' )->from ( '{lz_text_reply}' )->where (array ('keyword' => $keyword ,'deleted'=>0))->get (0);
        if(!$rs['content']){
            return $msg;
        }else{
            return $rs['content'];
        }
    }
}
