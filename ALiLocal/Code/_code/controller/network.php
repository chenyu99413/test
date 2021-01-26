<?php
class Controller_Network extends Controller_Abstract {
	/**
	 * 网络查询
	 */
	function actionSearch() {
		$result = array ();
		foreach ( Network::find ()->order ( 'update_time desc' )->getAll () as $network ) {
			$result [] = array (
				"id" => $network->network_id,
				"code" => $network->network_code,
				"name" => $network->network_name,
			);
		}
		$this->_view ["items"] = $result;
	}
	
	/**
	 * 网络编辑
	 */
	function actionEdit() {
		//网络ID
		$network = new Network ();
		if (request ( "id" ) != null) {
			//网络
			$network = Network::find ( "network_id = ?", request ( "id" ) )->getOne ();
		}
		
		if (request_is_post ()) {
			$conn = QDB::getConn ();
			$conn->startTrans ();
			
			//网络
			if (request ( "network" ) != null || strlen ( request ( "network" ) ) > 0) {
				$network->changeProps ( request ( "network" ) );
				$network->save ();
			}
			$conn->completeTrans ();
			return $this->_redirectMessage ( "网络保存", "保存成功", url ( "network/edit", array (
				"id" => $network->network_id 
			) ) );
		}
		
		$this->_view ["network"] = $network;
	}
	/**
	 * 保存燃油
	 */
	function actionNetworkfuelsave(){
	    //燃油附加费
	    if (request('networkfuel')){
	        $p = request ( "networkfuel" );
	        $networkfuel = Networkfuel::find ( "network_fuel_id = ?", $p ["network_fuel_id"] )->getOne ();
	        $networkfuel->network_id = request('network_id');
	        $p['effective_date']=strtotime($p['effective_date']."00:00:00");
	        $p['fail_date']=strtotime($p['fail_date']."23:59:59");
	        $networkfuel->changeProps ( $p );
	        $networkfuel->save ();
	        echo ($networkfuel->network_fuel_id);
	    }
	    exit();
	}
	/**
	 * @todo   保存国家-发票模板
	 * @author stt
	 * @since  2020-11-05
	 * @link   #83311
	 */
	function actionCountryInvoicesave(){
		//国家-发票模板
		$p = request('countryinvoice');
		if ($p){
			$countryinvoice = CountryInvoice::find ( "id = ?", request('id'))->getOne ();
			///网络ID
			$countryinvoice->network_id = request('network_id');
			$type = $p['invoice_type'];
			//发票模板名称对照
			$p['invoice_type'] = CountryInvoice::$fan_invoice_type[$type];
			$countryinvoice->changeProps ($p);
			$countryinvoice->save ();
			echo ($countryinvoice->id);
		}
		exit();
	}
	
	/**
	 * 燃油删除
	 */
	function actionDelete() {
		if (request ( "network_fuel_id" )) {
			$networkfuel = Networkfuel::find ( "network_fuel_id = ?", request ( "network_fuel_id" ) )->getOne ();
			$networkfuel->destroy ();
		}
		exit();
	}
	/**
	 * @todo   删除国家-发票模板
	 * @author stt
	 * @since  2020-11-05
	 * @link   #83311
	 */
	function actionDecountryinvoice() {
		if (request ( "id" )) {
			//删除
			$countryinvoice = CountryInvoice::find ( "id = ?", request ( "id" ) )->getOne ();
			$countryinvoice->destroy ();
		}
		exit();
	}
	
	/**
	 * 检查网络
	 */
	function actionChecknetwork() {
	    $network = Network::find ( "network_code= ?", request ( "value" ) )->getOne ();
	    if (!$network->isNewRecord () && ($network->network_id!=request('network_id'))) {
	        echo "false";
	    } else {
	        echo "true";
	    }
	    exit ();
	}
}