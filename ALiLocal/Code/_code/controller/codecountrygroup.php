<?php

class Controller_CodeCountryGroup extends Controller_Abstract{
	function actionSearch() {
	}
	function actionList() {
		$page = intval ( request ( 'page', 1 ) );
		$page_size = intval ( request ( 'page_size', 30 ) );
		$pagination = null;
	
		$select = CodeCountryGroup::find ();
	
		if (request ( 'name' )) {
			$name = request ( 'name' );
			$select->where ( 'name like ?', "%{$name}%" );
		}
	
		$countrygroups = $select->limitPage ( $page, $page_size )
		->fetchPagination ( $pagination )
		->getAll ();
	
		$this->_view ['countrygroups'] = $countrygroups;
		$this->_view ['pagination'] = $pagination;
	}
	function actionEditModal() {
		$group = CodeCountryGroup::find ( 'id = ?', request ( 'countrygroup_id' ) )->getOne ();
		$checked = explode(',', $group->country_codes);
		$this->_view ['group'] = $group;
		$this->_view ['checked'] = $checked;
	}
	function actionEditModalSave(){
		if (! request_is_ajax ()) {
			return $this->_redirectAjax ( false );
		}
		$group_check = CodeCountryGroup::find ( 'name = ?', request ( 'name' ) )->getOne ();
		if (! $group_check->isNewRecord () && request ( 'countrygroup_id' )<>$group_check->id) {
			return $this->_redirectAjax ( false, '该国家组已存在' );
		}
		// 保存数据
		$countrygroup = new CodeCountryGroup();
		if (request ( 'countrygroup_id' )) {
			$countrygroup = CodeCountryGroup::find ( 'id = ?', request ( 'countrygroup_id' ) )->getOne ();
			if ($countrygroup->isNewRecord ()) {
				return $this->_redirectAjax ( false, '数据错误' );
			}
		}
		$country_code_str = '';
		if (request ( 'countrygroup_codes' )) {
			$country_code_str = join ( ',', request ( 'countrygroup_codes' ) );
		}
		$countrys_save = array ();
		if (request ( "countrylist" )) {
			$countrys = explode ( "\r\n", request ( "countrylist" ) );
			foreach ( $countrys as $country ) {
				$check_country = Country::find ( "code_word_two = ? or english_name = ? or english_name2 = ? or chinese_name = ?", $country, $country, $country, $country )->getOne ();
				if (! $check_country->isNewRecord ()) {
					$countrys_save [] = $check_country->code_word_two;
				}
			}
		}
		$countrys_save = array_filter ( $countrys_save );
		$countrys_save = array_unique ( $countrys_save );
		$countrys_str = join ( ',', $countrys_save );
		$countrygroup->name = request('name');
		$countrygroup->country_codes = strlen($countrys_str)>0?$countrys_str:$country_code_str;
		$countrygroup->save ();
		return $this->_redirectAjax ( true, '保存成功' );
	}
}

?>