<?php
class Controller_Role extends Controller_Abstract {
	/**
	 * 角色管理
	 */
	function actionSearch() {
	    $roles=Role::find()->getAll();
	    foreach ($roles as $role){
	        $data['role_name']=$role->role_name;
	        $data['role_id']=$role->role_id;
	        $data['staff_names']='';
	        $staffs=StaffRole::find('role_id=?',$role->role_id)->joinLeft('tb_staff', '','tb_staff.staff_id=tb_staff_role.staff_id')
	        ->where('tb_staff.status = "1"')
	        ->getAll();
	        foreach ($staffs as $temp){
	            $data['staff_names'].=$temp->staff->staff_name.',';
	        }
	        $data['staff_names']=trim($data['staff_names'],',');
	        $result[]=$data;
	    }
	    $this->_view['roles']=$result;
	}
	/**
	 * 角色编辑
	 */
	function actionEdit(){
	    $role = new Role ();
	    $purviews = array ();
	    if (request ( "role_id" ) != null) {
	        $role = Role::find ( "role_id = ?", request ( "role_id" ) )->getOne ();
	        $rolePurviews = RolePurview::find ( "role_id = ?", request ( "role_id" ) )->getAll ();
	        $purviews = Helper_Array::getCols ( $rolePurviews, "purview_path" );
	    }
	    
	    // 保存
	    if (request_is_post ()) {
	        $conn = QDB::getConn ();
	        $conn->startTrans ();
	        	
	        if (request ( "role_name" ) != null || strlen ( request ( "role_name" ) ) > 0) {
	            $role->role_name = request ( "role_name" );
	            $role->save ();
	        }
	        if (request ( "purviews" ) != null || strlen ( request ( "purviews" ) ) > 0) {
	            RolePurview::meta ()->destroyWhere ( "role_id = ?", $role->role_id );
	            foreach ( json_decode ( request ( "purviews" ) ) as $value ) {
	                $purview = ( array ) $value;
	                $rolePurview = new RolePurview ();
	                $rolePurview->role_id = $role->role_id;
	                $rolePurview->purview_name = $purview ["name"];
	                $rolePurview->purview_path = $purview ["path"];
	                $rolePurview->save ();
	            }
	        }
	        	
	        $conn->completeTrans ();
	        return $this->_redirectMessage ( "权限保存", "保存成功", url ( 'role/search' ) );
	    }
	    
	    $this->_view ["role"] = $role;
	    $this->_view ["purviews"] = $purviews;
	}
}