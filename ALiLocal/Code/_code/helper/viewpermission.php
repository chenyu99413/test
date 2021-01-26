<?php
/**
 * @todo   仅查看权限
 * @author stt
 * @since  August 26th 2020
 * @link   #82128
 */
class Helper_ViewPermission{
	/**
	 * @todo   判断是否是“审计专用”角色
	 * @author stt
	 * @since  August 26th 2020
	 * @link   #82128
	 */
	static function isAudit() {
		$current_user = MyApp::currentUser("staff_name");
		$staffrole=StaffRole::find('staff_id = ? and role_id =?',MyApp::currentUser('staff_id'),"19")->getOne();
		if($staffrole->isNewRecord()){
			//能查看
			return true;
		}else{
			//是这个角色不能查看
			return false;
		}
		
	}
}