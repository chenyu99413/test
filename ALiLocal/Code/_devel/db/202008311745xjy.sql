ALTER TABLE `tb_department`
ADD COLUMN `status`  int(2) NULL DEFAULT 0 COMMENT '部门类型  0：内部仓 1：外包仓' AFTER `level`;