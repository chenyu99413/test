<?php
/**
 * 泛远 OA 接口
 *
 */
class Controller_OA extends Controller_Abstract {
	const OAURL='http://oa.far800.com';
	const APIKEY='aliexpress';
	function _before_execute(){
		if (request('apikey')!=self::APIKEY){
			exit('access decline');
		}
	}
	static function getLoginURL(){
		return 'https://oa.far800.com/oa_login?go='.urlencode('http://'.$_SERVER['HTTP_HOST'].url('oa/ticket',array('apikey'=>self::APIKEY)));
	}
	function actionTicket(){
		if (request('ticket')){
			$data=Helper_Curl::get1(self::OAURL.'/api/checkTicket?ticket='.request('ticket'));
			QLog::log("data:".$data);
			$data=json_decode($data,true);
			if ($data['code'] >0 || !is_array($data)){
				return $this->_redirectMessage('验证失败','无法验证您的帐号，请重新登录', 'http://oa.far800.com');
			}else {
				session_start();
				//登录
				$u=self::updateUser($data['data']);
				MyApp::changeCurrentUser ($u->toArray(), 'MEMBER' );
				return $this->_redirect ( url ( 'staff/index' ) );
			}
		}else {
			exit('Error');
		}
	}
	function actionAPI(){
		if (strtoupper(request('do'))=='UPDATEUSER'){
			$body=file_get_contents("php://input");
			$body=json_decode($body,true);
			self::updateUser($body);
			return self::success('Updated');
		}
		return self::failure('Request invalid.');
	}
	static function failure($message,$code='1000',$data=''){
		return json_encode(array('code'=>$code,'data'=>$data,'message'=>$message));
	}
	static function success($str,$message=''){
		return json_encode(array('code'=>'0','data'=>$str,'message'=>$message));
	}
	/**
	 * 根据 OA 的资料更新本地用户资料
	 * @param array $data
	 * @return User_Zhiyou
	 */
	static function updateUser($data){
		if (isset($data['username'])){
			$u=Staff::find('staff_code=?',$data['username'])->getOne();
			if (!$u->department_id){
				$u->department_id=6;
			}
			$u->changeProps(array(
				'staff_code'=>$data['username'],
				'staff_name'=>$data['name'],
				'status'=>$data['state'] ==2?0:1
			));
			return $u->save();
		}else {
			throw new QException('Data Invalid');
		}
	}
}
