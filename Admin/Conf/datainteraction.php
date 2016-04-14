<?php 
/**
 * 数据交互设置
 */

return array(
	'MisAutoCdk'=>array( //出差申请
			'sql'=>"
				SELECT
						ryinfo.yongyouorderno AS 创建人编码,
						FROM_UNIXTIME(mis_auto_xeyis.`chuchajieshuoriqi`,'%Y-%m-%d %H:%i:%s') AS 结束时间,
						ryinfochuc.`name` AS 批准人,
						ryinfo.`name` AS 创建人,
						FROM_UNIXTIME(mis_auto_xeyis.`chuchakaishiriqi`,'%Y-%m-%d %H:%i:%s') AS 开始时间,
						FROM_UNIXTIME(mis_auto_xeyis.`createtime`,'%Y-%m-%d %H:%i:%s') AS 创建时间,
						ryinfochucr.yongyouorderno AS 出差人编码,
						mis_auto_vqzzh.`name` AS 出差类型,
						mis_auto_xeyis.chuchashiyou AS 出差事由,
						'' AS 备注,
						ryinfochuc.`name` AS 审核人名,
						FROM_UNIXTIME(process_info_history.`dotime`,'%Y-%m-%d %H:%i:%s') AS 审核时间,
						(
							CASE
							WHEN mis_auto_xeyis.`operateid` = '0' THEN
								0
							WHEN mis_auto_xeyis.`operateid` = '1' THEN
								1
							END
						) AS 是否审核,
						ryinfochuc.yongyouorderno AS 审核人编码
						FROM
							mis_auto_xeyis -- 出差申请审批表
						LEFT JOIN mis_auto_vqzzh ON mis_auto_xeyis.`chuchaleixing` = mis_auto_vqzzh.`orderno` -- 出差类型表
						LEFT JOIN ryinfo ON mis_auto_xeyis.createid = ryinfo.id -- 获取创建人名称编码
						LEFT JOIN process_info_history ON mis_auto_xeyis.id = process_info_history.`tableid`
						AND process_info_history.`tablename` = 'MisAutoCdk'
						AND process_info_history.doinfo = '流程结束'
						LEFT JOIN ryinfo AS ryinfochuc ON process_info_history.`userid` = ryinfochuc.id -- 获取审核人名以及编码
						LEFT JOIN ryinfo AS ryinfochucr ON mis_auto_xeyis.`shenqingren` = ryinfochucr.id -- 出差人编码
						LEFT JOIN mis_system_company ON mis_auto_xeyis.`shenqingzhuti` = mis_system_company.`id` -- 获取公司编码
						LEFT JOIN mis_system_department ON mis_auto_xeyis.`shenqingbumen` = mis_system_department.`id` -- 获取部门编码
						WHERE
						mis_auto_xeyis.`operateid` = '1' -- 是否终审	
				",
			'dbsource'=>'14',
			'proname'=>'pmpcc',
			'url'=>'http://192.168.0.238:8088/smartESBProject/services/dbService/procedure/4',
			),
	'MisAutoYxv'=>array( //请假单
			'sql'=>"
					SELECT
					  	`ryinfo`.`yongyouorderno`                AS `创建人编码`,
					  	DATE_FORMAT(FROM_UNIXTIME(`mis_auto_ucjcm`.`jieshuriqi`),'%Y-%m-%d %H:%i:%s') AS `结束时间`,
					  	`mis_auto_dtics`.`u8leixingbianma`       AS `请假类型`,
					 	`ryinfoshenhe`.`name`                    AS `批准人`,
					  	`ryinfo`.`name`                          AS `创建人名`,
					  	DATE_FORMAT(FROM_UNIXTIME(`mis_auto_ucjcm`.`kaishiriqi`),'%Y-%m-%d %H:%i:%s') AS `开始时间`,
					  	DATE_FORMAT(FROM_UNIXTIME(`mis_auto_ucjcm`.`createtime`),'%Y-%m-%d %H:%i:%s') AS `创建时间`,
					  	`ryinfoqingjr`.`yongyouorderno`          AS `请假人编码`,
					  	`mis_auto_ucjcm`.`qingjiashiyou`         AS `请假原因`,
					  	''                                       AS `备注`,
					  	DATE_FORMAT(FROM_UNIXTIME(`mis_auto_ucjcm`.`kaishiriqi`),'%Y-%m-%d %H:%i:%s') AS `计划时间`,
					  	`ryinfoshenhe`.`yongyouorderno`          AS `审核人编码`,
					  	`ryinfoshenhe`.`name`                    AS `审核人`,
					  	DATE_FORMAT(FROM_UNIXTIME(`process_info_history`.`dotime`),'%Y-%m-%d %H:%i:%s') AS `审核时间`,
					  	(CASE WHEN mis_auto_ucjcm.auditState!=3 THEN '否'
					        WHEN mis_auto_ucjcm.auditState=3 THEN '是'
					        END ) AS 审核状态
					 	-- `mis_system_company`.`yongyouorderno`    AS `公司编码`,
					 	-- `mis_system_department`.`yongyouorderno` AS `部门编码`
						FROM (((((((`mis_auto_ucjcm`
					    	LEFT JOIN `mis_auto_dtics`
					           	ON ((`mis_auto_ucjcm`.`qingjialeixing` = `mis_auto_dtics`.`id`)))
					        LEFT JOIN `ryinfo`
					          	ON ((`mis_auto_ucjcm`.`createid` = `ryinfo`.`id`)))
					       	LEFT JOIN `process_info_history`
					         	ON (((`mis_auto_ucjcm`.`id` = `process_info_history`.`tableid`)
					              	AND (`process_info_history`.`tablename` = 'MisAutoYxv')
					              	AND (`process_info_history`.`doinfo` = '流程结束'))))
					      	LEFT JOIN `ryinfo` `ryinfoshenhe`
					        	ON ((`process_info_history`.`userid` = `ryinfoshenhe`.`id`)))
					     	LEFT JOIN `ryinfo` `ryinfoqingjr`
					       		ON ((`mis_auto_ucjcm`.`shenqingren` = `ryinfoqingjr`.`id`)))
					    	LEFT JOIN `mis_system_company`
					      		ON ((`mis_auto_ucjcm`.`shenqingzhuti` = `mis_system_company`.`id`)))
					   		LEFT JOIN `mis_system_department`
					     		ON ((`mis_auto_ucjcm`.`shenqingbumen` = `mis_system_department`.`id`)))	
			",
			'dbsource'=>'14',
			'proname'=>'pmpqjwsc',
			'url'=>'http://192.168.0.238:8088/smartESBProject/services/dbService/procedure/4',
			),
			'MisAutoKhh'=>array( //生成凭证
			'sql'=>"
			SELECT
		  `mis_auto_abqyr`.`orderno`                           AS `bianhao`,
		  DATE_FORMAT(FROM_UNIXTIME(`mis_auto_abqyr`.`huijiqijian`),'%m') AS `kuaijiyue`,
		  DATE_FORMAT(FROM_UNIXTIME(`mis_auto_abqyr`.`createtime`),'%Y-%m-%d %H:%i:%s') AS `zhidanriqi`,
		  `use_zhidanr`.`name`                                 AS `zhidanren`,
		  `mis_auto_abqyr_sub_pingzhengmingxi`.`zhaiyao`       AS `zhaiyao`,
		  `mis_auto_abqyr_sub_pingzhengmingxi`.`kemubianma`    AS `kemu`,
		  `mis_auto_abqyr_sub_pingzhengmingxi`.`jiefang`       AS `jiefang`,
		  DATE_FORMAT(FROM_UNIXTIME(`mis_auto_abqyr`.`huijiqijian`),'%Y') AS `kuaijinian`,
		  DATE_FORMAT(FROM_UNIXTIME(`mis_auto_abqyr`.`huijiqijian`),'%Y%m') AS `kuaijiqijian`,
		  `mis_auto_abqyr_sub_pingzhengmingxi`.`daifang`       AS `daifang`,
		  `usercreate`.`name`                                  AS `yewuyuan`,
		  `usercreate`.`yongyouorderno`                        AS `yewuyuanbianhao`,
		  `mis_system_department`.`yongyouorderno`             AS `bumenbianma`,
		  `mis_auto_abqyr_sub_pingzhengmingxi`.`xiangmu`       AS `xiangmu`,
		  `mis_auto_abqyr_sub_pingzhengmingxi`.`kehu`          AS `kehu`,
		  `mis_auto_abqyr_sub_pingzhengmingxi`.`gongyingshang` AS `gongyingshang`,
		  `mis_auto_wgpqu`.`name`                              AS `pingzhengleibie`,
		  `mis_auto_abqyr_sub_pingzhengmingxi`.`id`            AS `hanghao`
	FROM `mis_auto_abqyr`
       LEFT JOIN `mis_auto_abqyr_sub_pingzhengmingxi`
         ON `mis_auto_abqyr`.`id` = `mis_auto_abqyr_sub_pingzhengmingxi`.`masid`
      LEFT JOIN `mis_system_department`
        ON `mis_auto_abqyr_sub_pingzhengmingxi`.`bumen` = `mis_system_department`.`id`
     LEFT JOIN `user` `usercreate`
       ON `mis_auto_abqyr_sub_pingzhengmingxi`.`yewuyuan` = `usercreate`.`id`
    LEFT JOIN `user` `use_zhidanr`
      ON `mis_auto_abqyr`.`createid` = `use_zhidanr`.`id`
   LEFT JOIN `mis_auto_wgpqu`
     ON `mis_auto_abqyr`.`pingzhengleibie` = `mis_auto_wgpqu`.`id`
     WHERE mis_auto_abqyr.`operateid`=1 
			",
			'dbsource'=>'30',
			'proname'=>'Input_pingzheng',
			'url'=>'http://192.168.0.238:8088/smartESBProject/services/dbService/procedure/4',
			),
		

		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
);
?>