<?php
/**
 * @Title: htViewModel
 * @Package package_name
 * @Description: todo(动态表单_自动生成-合同视图)
 * @author 杨君
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-03-25 14:58:25
 * @version V1.0
*/
class htViewModel extends ViewModel {
	public $viewFields = array(
		'mis_auto_bklne'=>array('_as'=>'mis_auto_bklne','projectid','xiangmubianma','xiangmumingchen','kehumingchen','dangqiankehmc','zhuti','xingye','chanyelian','yewuleixing','shangjibianhao','canzhaoxiangmubianha','zixunxiangmubianma','zhudiao','fudiao','daikuanyongtu','daikuanyongtumiaoshu','rongzifangan','zaibaojine','shenqingjine','shenqingyewuqixian','yifangkuanjine','shengyujine','pifuqixian','pifujine','lixiangbumen','lixiangren','fengxiandengji','fengxianxiangmuzhuan','fengxianxiangmulixia','lixiangriqi','kehudizhi','youbian','xiangmujieduan','yewujianyijine','yewujianyiqixian','fengkongjianyijine','fengkongjianyiqixian','zhuanjiajianyijine','zhuanjiajianyililv','pifuchujuriqi','canzhaoxiangmuyewule','canzhaoxiangmufengxi','zhutileixing','zhuanjiajianyiqixian','yewuyuanjianyijine','yewuyuanjianyiqixian','fengkongyuanjianyiji','fengkongyuanjianyiqi','yewuyuanyijian','fengkongyuanyijian','yewubuyijian','fengkongbuyijian','zhuanjiayijian','_type'=>'LEFT'),
		'mis_auto_fqtej'=>array('_as'=>'mis_auto_fqtej','orderno'=>'zhuhetongbianma','pifudanhao','hezuoyinxing','jiekuanhetonghao','jiekuanhetongmingche','jiekuanjine','jiekuanhetongqiandin','baozhenghetonghao','baozhenghetongqiandi','weituodaikuanjiekuanhetonghao','weituodaikuanweituohetonghao','fangkuanriqi'=>'yinghangfangkuanriqi','huaikuanriqi'=>'yinghanghuankuanriqi','weituobaozhenghetong','weituobaozhenghetongk','lvxingzhaiwukaishiri','lvxingzhaiwujiezhiri','_on'=>'mis_auto_bklne.xiangmubianma=mis_auto_fqtej.xiangmubianma','_type'=>'LEFT'),
		'mis_auto_zbotm'=>array('_as'=>'mis_auto_zbotm','fenshu','fandanbaocuoshibianh','fandanbaocuoshizhong','pinggujigou','_on'=>'mis_auto_fqtej.orderno=mis_auto_zbotm.zhuhetonghao','_type'=>'LEFT'),
		'mis_auto_offsz'=>array('_as'=>'mis_auto_offsz','gongsimingchen','zhusuodi'=>'jiafangzhusuodi','zhuyaojingyingjigous'=>'jiafangzhuyaojingyingjigous','farendaibiao'=>'jiafangfarendaibiao','kaihuyinxing'=>'jiafangkaihuyinxing','kaihuyinxingzhanghao'=>'jiafangkaihuyinxingzhanghao','dianhuahaoma'=>'jiafangdianhuahaoma','chuanzhen'=>'jiafangchuanzhen','youbian'=>'jiafangyoubian','_on'=>'mis_auto_bklne.companyid=mis_auto_offsz.gongsimingchen','_type'=>'LEFT'),
		'mis_auto_banmo'=>array('_as'=>'mis_auto_banmo','zhusuodi'=>'yifangzhusuodi','zhuyaojingyingjigous'=>'yifangzhuyaojingyingjigous','farendaibiao'=>'yifangfarendaibiao','kaihuyinxing'=>'yifangkaihuyinxing','kaihuzhanghu'=>'yifangkaihuzhanghu','nashuirendengjihao'=>'yifangnashuirendengjihao','dianhuahaoma'=>'yifangdianhuahaoma','chuanzhen'=>'yifangchuanzhen','youzhengbianma'=>'yifangyouzhengbianma','_on'=>'mis_auto_bklne.kehumingchen=mis_auto_banmo.orderno'),
);
}
?>