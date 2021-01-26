<?php
class Controller_WXApi extends Controller_Abstract{
	 //成功
	const ECODE_SUCCESS = '10000';
	 //微信接口报错
	const ECODE_ERROR1 = '10001';
	//缓存失效
	const ECODE_ERROR2 = '10002';
	 //错误
	const ECODE_ERROR3 = '10003';
	//测试
	function actionTest(){
		$a = request('code');
		return $a.'333';
		exit;
	}
	/**
	 * @todo   登录
	 * @author 吴开龙
	 * @since  2020-10-29 09:10:54
	 * @param
	 * @return
	 * @link   #81390
	 */
	function actionLogin(){
		$code = request('code');
		//获取用户信息
		$userinfo = json_decode(request('userinfo'),true);
		$url = "https://api.weixin.qq.com/sns/jscode2session?appid=wx518afd2666887747&secret=8d1216a9951e392a1c9a63ebbf65bf42&js_code=".$code."&grant_type=authorization_code";
		$return = Helper_Curl::get($url);
		$return = json_decode($return,true);
		if(isset($return['openid']) && $return['openid']){
			//接口获取openid成功
// 			$pick = PickUpMember::find('wechat_id =?',$return['openid'])->getOne();
// 			//判断用户如果不存在则新增
// 			if($pick->isNewRecord()){
// 				$pick->
// 			}
			//openid做缓存的值
			$openid_session = array (
				'openid' => $return['openid'],
				'session_key' => $return ['session_key'] 
			);
			//随机数 做key 并下发到小程序做登录态
			$data['rand'] = $this->getRandom ( 32 );
			Q::writeCache($data['rand'], $openid_session, array('life_time'=> 86400,'serialize'=> true));
			$pick = PickUpMember::find('wechat_id =?',$return['openid'])->getOne();
			if($pick->isNewRecord()){
				$data['status'] = 0;
				//添加数据
				if($pick->isNewRecord()){
					$pick->status = '0';
				}
				$pick->wechat_id = $return['openid'];
				$pick->wechat_no = $userinfo['nickName'];
				//$pick->name = request('');
				$pick->img_url = $userinfo['avatarUrl'];
				$pick->gender = $userinfo['gender'] == 1 ? '男' : '女';
				$pick->save();
			}else{
				$data['status'] = $pick->status;
			}
			return self::success ( self::ECODE_SUCCESS, '', $data );
		}else{
			//接口获取失败，返回错误码等信息
			return self::success ( self::ECODE_ERROR1, '微信登录失败' );
		}
	}
	/**
	 * @todo   获取用户审核信息
	 * @author 吴开龙
	 * @since  2020-10-29 11:30:54
	 * @param
	 * @return
	 * @link   #81390
	 */
	function actionSaveUser(){
		//获取小程序传来的key
		$key = request('key');
		$userinfo = json_decode(request('userInfo'),true);
		//return self::success ( self::ECODE_SUCCESS, $userinfo );
		//获取保存在缓存中的数据
		$wxdata = Q::cache ( $key );
		if($wxdata === false){
			//缓存失效
			return self::success ( self::ECODE_ERROR2, '缓存失效' );
		}
		//保存用户信息
		$pick = PickUpMember::find('wechat_id =?',$wxdata['openid'])->getOne();
		if($pick->isNewRecord()){
			$status = '0';
		}else{
			$status = $pick->status;
		}
		
		return self::success ( self::ECODE_SUCCESS, '成功' ,$status);
	}
	/**
	 * @todo   扫码返回
	 * @author 吴开龙
	 * @since  2020-12-04 14:30:54
	 * @param
	 * @return
	 * @link   #81390
	 */
	function actionScanCode(){
		$order = Order::find ( 'ali_order_no=? or reference_no=? or tracking_no=?', request('order_no'), request('order_no'), request('order_no') )->getOne ();
		if($order->isNewRecord()){
			return self::success ( self::ECODE_ERROR3, '订单不存在');
		}
		return self::success ( self::ECODE_SUCCESS, '成功');
	}
	/**
	 * @todo   批量上传图片
	 * @author 吴开龙
	 * @since  2020-12-04 11:30:54
	 * @param
	 * @return
	 * @link   #81390
	 */
	function actionBatchImg(){
		$uploader = new Helper_Uploader ();
		if (! $uploader->existsFile ( 'imagefile' )) {
			return self::success ( self::ECODE_ERROR3, '无文件');
		}
		$is_file = file_get_contents($_FILES['imagefile']['tmp_name']);
		$uploader = $uploader->file ( 'imagefile' );
		$order = Order::find ( 'ali_order_no=? or reference_no=? or tracking_no=?', request('order_no'), request('order_no'), request('order_no') )->getOne ();
		if($order->isNewRecord()){
			return self::success ( self::ECODE_ERROR3, '订单不存在');
		}
		//随机数
		$file_count = rand(1000,9999);
		//文件名
		$postfix_name = $order->ali_order_no . '-' . $file_count . '.' . $uploader->extname();
		//上传到阿里oss
		$res = Helper_AlipicsOssImg::uploadAlistrings($postfix_name,$is_file);
		QLog::log('wx_ali_img_url:'.$res );
		if(!$res){
			return self::success ( self::ECODE_ERROR3, '上传失败');
		}
// 		//file_path
// 		$file_path = '/www/ali1688/public/upload/files/' . date ( 'Ymd' ) . '/' . $postfix_name;
// 		//创建文件夹以及保存文件
// 		$root = INDEX_DIR;
// 		$fileurl = DS . 'public' . DS . 'upload' . DS . 'files' . DS . date ( 'Ymd' );
// 		$filename = DS . $postfix_name;
// 		Helper_Filesys::mkdirs ( $root . $fileurl );
// 		$uploader->move ( $root . $fileurl . $filename );
		//保存数据
		$key = request('key');
		//获取保存在缓存中的数据
		$wxdata = Q::cache ( $key );
		$wechat = PickUpMember::find('wechat_id=?',$wxdata['openid'])->getOne();
		$file = new File ();
		$file->order_id = $order->order_id;
		$file->file_name = $postfix_name;
		$file->file_path = $res;
		$file->operator = $wechat->name;
		$file->save ();
		//修改订单上传图片状态
		$order->is_picture=2;
		$order->save();
		$data = array (
			'code' => 1,
			'msg' => '成功'
		);
		return self::success ( self::ECODE_SUCCESS, '成功');
	}
	/**
	 * @todo   接口返回数据
	 * @author 吴开龙
	 * @since  2020-10-29 11:10:54
	 * @param
	 * @return
	 * @link   #81390
	 */
	static function success($code, $msg = '', $data = ''){
		$arr = json_encode(array(
			'code' => $code,
			'msg'  => $msg,
			'data' => $data
		));
		QLog::log('wxapisuccess:'.$arr);
		return $arr;
	}
	/**
     * @todo   生成随机数
     * @author 吴开龙
     * @since  2020-10-29 11:00:54
     * @param
     * @return
     * @link   #81390
     */
	function getRandom($param){
		$str="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$key = "";
		for($i=0;$i<$param;$i++)
		{
			$key .= $str{mt_rand(0,32)};    //生成php随机数
		}
		return $key;
	}
}