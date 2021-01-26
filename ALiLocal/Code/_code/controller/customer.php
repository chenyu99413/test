<?php
class Controller_Customer extends Controller_Abstract{
	/**
	 * 检索
	 */
	function actionSearch(){
		$select=Customer::find();
		$active_id = 0;
		if (request ( 'parameters' ) != null) {
			// 合同未签订客戶(没有合同号或者没有合同签订日期)
			if (request ( 'parameters' ) == 'contract_date') {
				$select->where ( "contract_code is  null OR contract_code= '' OR contract_date is null OR contract_date= '' OR contract_expiration_date is null OR contract_expiration_date= '' " );
				$active_id = 1;
			}
			
			// 合同快到期客戶(合同到期日期还有一个月)
			if (request ( 'parameters' ) == 'contract_expiration_date') {
				//获取在当前日期一个月之后的时间
				$end_date = date ( "Y-m-d H:i:s", strtotime ( "+30days" ) );
				$select->where ( "contract_expiration_date between ? And ? And contract_code is not null And contract_code!='' And contract_date is not null And contract_date!='' ", date ( 'Y-m-d H:i:s' ), $end_date );
				$active_id = 2;
			}
			// 合同已到期客戶(合作，包括待定)
			if (request ( 'parameters' ) == 'contract_expire_co') {
				$select->where ( "contract_expiration_date < ? And contract_code is not null And contract_code!='' And contract_date is not null And contract_date!='' And status !='1' ", date ( 'Y-m-d H:i:s' ) );
				$active_id = 3;
			}
			// 合同已到期客戶(不合作)
			if (request ( 'parameters' ) == 'contract_expire_nonco') {
				$select->where ( "contract_expiration_date < ? And contract_code is not null And contract_code!='' And contract_date is not null And contract_date!='' And status ='1' ", date ( 'Y-m-d H:i:s' ) );
				$active_id = 4;
			}
			// 合同有效客戶(必须是合作的，有合同号，有生效日期和失效日期，失效日期必须在当前日期之后)
			if (request ( 'parameters' ) == 'valid_contractNo') {
				$select->where ( "status !='1' && contract_code is  not null && contract_code != '' &&  contract_date is not null &&  contract_date != '' &&  contract_expiration_date is not null  &&  contract_expiration_date != '' && contract_expiration_date > ? " ,date ( 'Y-m-d H:i:s' ));
				$active_id = 5;
			}
		}
		$counts = array ();
		//所有客戶总数
		$counts [0] = Customer::find ()
		->getCount ();
		//合同未签订客戶总数
		$counts [1] = Customer::find ( "contract_code is  null  OR contract_code= '' OR contract_date is null OR contract_date= '' OR contract_expiration_date is null OR contract_expiration_date= '' " )
		->getCount ();
		//合同快到期客戶总数(合同到期日期还有一个月)
		//获取在当前日期一个月之后的时间
		$end_date = date ( "Y-m-d H:i:s", strtotime ( "+30days" ) );
		$counts [2] = Customer::find ()->where ( "contract_expiration_date between ? And ? And contract_code is not null And contract_code != '' And contract_date is not null And contract_date!= '' ", date ( 'Y-m-d H:i:s' ), $end_date )
		->getCount ();
		//合同已到期客戶总数（合作，包括待定）
		$counts [3] = Customer::find ()->where ( "contract_expiration_date < ? And contract_code is not null And contract_code!= '' And contract_date is not null And contract_date!= '' And status !='1' ", date ( 'Y-m-d H:i:s' ) )
		->getCount ();
		//合同已到期客戶总数(不合作)
		$counts [4] = Customer::find ()->where ( "contract_expiration_date < ? And contract_code is not null And contract_code!= '' And contract_date is not null And contract_date!= '' And status ='1' ", date ( 'Y-m-d H:i:s' ) )
		->getCount ();
		// 合同有效客戶(必须是合作的，有合同号，有生效日期和失效日期，失效日期必须在当前日期之后)
		$counts [5] = Customer::find ()->where ( "status !='1' && contract_code is  not null && contract_code != '' &&  contract_date is not null &&  contract_date != '' &&  contract_expiration_date is not null  &&  contract_expiration_date != '' && contract_expiration_date > ? " ,date ( 'Y-m-d H:i:s' ))
		->getCount ();
		$this->_view ['active_id'] = $active_id;
		$this->_view ['customers'] = $select->getAll();
		$this->_view ["tabs"] = $this->createTabs ( $counts );
	}
	
	/**
	 * 创建标签
	 */
	function createTabs($counts) {
		return array (
			array (
				"id" => "0","title" => "全部","count" => $counts [0],
				"href" => url ( "Customer/search", array (
					"id" => request ( "customer_id" ),
					"parameters" => "all"
				) )
			),
			array (
				"id" => "1","title" => "合同未签订","count" => $counts [1],
				"href" => url ( "Customer/search", array (
					"id" => request ( "customer_id" ),
					"parameters" => "contract_date"
				) )
			),
			array (
				"id" => "2","title" => "合同快到期","count" => $counts [2],
				"href" => url ( "Customer/search", array (
					"id" => request ( "customer_id" ),
					"parameters" => "contract_expiration_date"
				) )
			),
			array (
				"id" => "3","title" => "合同已到期(合作)","count" => $counts [3],
				"href" => url ( "Customer/search", array (
					"id" => request ( "customer_id" ),
					"parameters" => "contract_expire_co"
				) )
			),
			array (
				"id" => "4","title" => "合同已到期(不合作)","count" => $counts [4],
				"href" => url ( "Customer/search", array (
					"id" => request ( "customer_id" ),
					"parameters" => "contract_expire_nonco"
				) )
			),
			array (
				"id" => "5","title" => "合同有效(合作)","count" => $counts [5],
				"href" => url ( "Customer/search", array (
					"id" => request ( "customer_id" ),
					"parameters" => "valid_contractNo"
				) )
			)
		);
	}
	/**
	 * 客户编辑
	 */
	function actionEdit(){
		$customer = Customer::find('customer_id = ?',request('customer_id'))->getOne();
		$title=array();
		if(request('customer_id')){
			$title=Title::find('customer_id = ?',request('customer_id'))->asArray()->getAll();
		}
		if(request_is_post()){
			//判断供应商名称是否存在
			$customer_check=Customer::find('customer = ?',request('customer'))->getOne();
			$customer_code_check = Customer::find('customs_code = ?',request('customs_code'))->getOne();
			if(!$customer_check->isNewRecord() && ($customer_check->customer_id!=request('customer_id'))){
				return $this->_redirectMessage('客户编辑失败', '客户名称已存在', url('/edit',array('customer_id'=>$customer->customer_id)),2);
			}elseif(!$customer_code_check->isNewRecord() && ($customer_code_check->customer_id!=request('customer_id'))){
				return $this->_redirectMessage('客户编辑失败', '客户代码已存在', url('/edit',array('customer_id'=>$customer->customer_id)),2);
			}else{
				if(!request('title_name')){
					return $this->_redirectMessage('客户编辑失败', '账号抬头不能为空', url('/edit',array('customer_id'=>$customer->customer_id)),2);
				}
				$customer=Customer::find('customer_id = ?',request('customer_id'))->getOne();
				$customer->customs_code = request('customs_code');
				$customer->customer = request('customer');
				$customer->contract_date = request('contract_date');
				$customer->contract_code = request('contract_code');
				//客户类型1线上2线下
				$customer->customer_type = request('customer_type');
				$customer->customer_sign = request('customer_sign');
				$customer->sender_mobile = request('sender_mobile');
				$customer->sender_telephone = request('sender_telephone');
				$customer->sender_email = request('sender_email');
				
				$customer->sender_name1 = request('sender_name1');
				$customer->sender_name2 = request('sender_name2');
				$customer->sender_street1 = request('sender_street1');
				$customer->sender_street2 = request('sender_street2');
				
				$customer->sender_country_code = request('sender_country_code');
				$customer->sender_city = request('sender_city');
				$customer->sender_postal_code = request('sender_postal_code');
				$customer->sender_state_region_code = request('sender_state_region_code');
				$customer->payment_rule = request('payment_rule');
				//删除check_ruleone
				$customer->check_ruletwo = request('check_ruletwo');
				$customer->status = request('status');
				$customer->contract_expiration_date = request('contract_expiration_date');
				//,隔开保存的客户对应的运输方式
				$customer->transports = request('relevant');
				//,隔开保存的客户对应的渠道
				$customer->channel = request('channel');
				
				$customer->save();
				Title::meta()->destroyWhere('customer_id =?',$customer->customer_id);
				foreach (request('title_name') as $name){
					$ti=new Title(array(
						'customer_id'=>$customer->customer_id,
						'name'=>$name
					));
					$ti->save();
				}
				return $this->_redirectMessage('客户编辑', '客户编辑成功', url('/search'));
			}
		}
		$this->_view['customer']=$customer;
		$this->_view['title']=$title;
	}
}

?>