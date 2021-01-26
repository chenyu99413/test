ALTER TABLE `tb_tracking` ADD `route_id` INT NOT NULL DEFAULT '0' COMMENT '和 tb_route表的关联' AFTER `update_time`;

update tb_tracking set confirm_flag=1 where send_flag=1;

update `tb_event` set confirm_flag =1 where send_flag=1;

DELETE FROM `tb_tracking` where route_id >0;
truncate table tb_routes;