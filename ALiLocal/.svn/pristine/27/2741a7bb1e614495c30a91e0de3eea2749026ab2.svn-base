<?php
class Controller_Department extends Controller_Abstract {
	/**
	 * 检索
	 */
	function actionSearch() {
	   $departments=Department::find()->getAll();
	   $this->_view['departments']=$departments;
	}
	/**
	 * 部门编辑
	 */
	function actionEdit(){
	    $department=Department::find('department_id=?',request('department_id'))->getOne();
	    if(request_is_post()){
	        $department->department_name=request('department_name');
	        $department->level=request('level');
	        $department->status=request('status');
	        $department->save();
	        return $this->_redirectMessage ( "部门保存", "保存成功", url ( "department/edit", array (
                "department_id" => $department->department_id
            ) ) );
	    }
	    $this->_view['department']=$department;
	}
	/**
	 * 部门树结构
	 */
	function actionDepartmenttree() {
	    $department_ids = RelevantDepartment::departmentids ();
	    //默认选中
	    $checkeds = array ();
	    if (request ( "checked" ) != null) {
	        $checkeds = explode ( ",", request ( "checked" ) );
	    }
	    //检索一级部门
	    $departments = Department::find ( "department_id in (?)", $department_ids )->getAll ();
	    foreach ( $departments as $department ) {
            $array [] = array (
                "id" => $department->department_id,
                "text" => $department->department_name,
                "checked" => in_array ( $department->department_id, $checkeds ) ? "checked" : "",
                "attributes" => ""
            );
	    }
	    echo (json_encode ( $array ));
	    exit ();
	}
	/**
	 * 校验部门名称唯一性
	 */
	function actionNamecheck(){
	    $department=Department::find('department_name=?',request('department_name'))->getOne();
	    if(!$department->isNewRecord() && $department->department_id!=request('department_id')){
	        echo 'error';
	    }else{
	        echo 'success';
	    }
	    exit();
	}
}