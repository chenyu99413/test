
create view `v_route_latest_ids` as
select max(id) as latest,tracking_no from tb_routes group by tracking_no;


create view `v_route_latest` as 
select `o`.`order_id` AS `order_id`,`o`.`ali_order_no` AS `ali_order_no`,`o`.`channel_id` AS `channel_id`,`rot2`.`tracking_no` AS `tracking_no`,`rot2`.`location` AS `location`,`rot2`.`time` AS `time`,`rot2`.`description` AS `description`,`rot2`.`create_time` AS `create_time` from ((`ali1688`.`tb_order` `o` left join `ali1688`.`v_route_latest_ids` `rot` on((`o`.`tracking_no` = `rot`.`tracking_no`))) left join `ali1688`.`tb_routes` `rot2` on(((`rot2`.`id` = `rot`.`latest`) and (`rot2`.`tracking_no` = `rot`.`tracking_no`)))) where ((`o`.`order_status` in (7,8)) or ((`o`.`order_status` = 12) and (`o`.`order_status_copy` in (7,8))))