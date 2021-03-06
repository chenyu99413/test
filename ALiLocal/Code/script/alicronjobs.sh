#!/bin/bash

pid_nums1=`ps aux | grep "/bin/bash.*alicronjobs.sh" | grep -v grep | wc -l`
if [ $pid_nums1 -gt "2" ]
then
    echo " I exit"
    exit
fi
#echo  " I do"

function checkprocess(){
    nowdate=`date +%y%m%d%H`
    pid_nums="0"
    pid_nums=`ps aux | grep "$*" | grep -v grep | wc -l`
    if [ $pid_nums -gt "1" ]
    then
        for pid in `ps aux | grep "$*" | grep -v grep|awk '{print $2}'`; do
          echo "${pid}";
          rundate=`ps -p "${pid}" -o lstart|grep ':'`;
          timestamp2=`date +%s -d"$rundate"` ;
          if [  `expr $timestamp2 + 43200 ` -gt $timestamp1 ]
          then
              echo "kill ${pid}"
              kill -9 "${pid}";              
          else
              echo "run ${pid}"
          fi
        done
        echo "  $pid_nums exit $* "
    fi
    
    pid_nums=`ps aux | grep "$*" | grep -v grep | wc -l`
    if [ $pid_nums -lt "1" ]
    then
        echo " $pid_nums  begin $*"
        if [ "${#4}" -gt "0" ]
        then
             php -c/etc/php_cli.ini $@ >> "log/${3:0:124}${4:0:55}_${nowdate}.log" &
        elif [ "${#3}" -gt "0" ]
        then
            php -c/etc/php_cli.ini $@ >> "log/${3:0:124}_${nowdate}.log" &
        fi
    fi
}

# go to www _devel root
cd  /www/ali1688/_devel

#推送事件
checkprocess ../_code/cli.php cron notifyBizEvent
#推送轨迹
checkprocess ../_code/cli.php cron notifyTrace
#推送面单
checkprocess ../_code/cli.php cron uploadRecordForm
#推送测试面单
#checkprocess ../_code/cli.php cron testuploadRecordForm
#抓取渠道末端轨迹
checkprocess ../_code/cli.php cron route
#抓取EMS末端轨迹
checkprocess ../_code/cli.php cron emsroute
#强制订阅EMS和DHL末端轨迹
checkprocess ../_code/cli.php cron RefRoute
#抓取fedex末端轨迹
checkprocess ../_code/cli.php cron fedexroute
#抓取us-fy(fsp)末端轨迹
#checkprocess ../_code/cli.php cron usroute
#抓取us-fy(俄速通)末端轨迹
#checkprocess ../_code/cli.php cron eusroute
#抓取us-fy(俄速通)末端轨迹
#checkprocess ../_code/cli.php cron neweUsRoute
#抓取dhl末端轨迹
#checkprocess ../_code/cli.php cron mldhlroute
#抓取dhle末端轨迹
#checkprocess ../_code/cli.php cron mxroute
#抓取麦链头程轨迹
#checkprocess ../_code/cli.php cron mltroute
#线下订单状态变成已签收
checkprocess ../_code/cli.php cron transfersign
#定时订阅已提取的末端轨迹
checkprocess ../_code/cli.php cron TimingRoute
#快手入库图片保存到File表
checkprocess ../_code/cli.php cron addfile
#usps抓取轨迹
checkprocess ../_code/cli.php cron IbRoute
#30天前的那一天已签收轨迹
checkprocess ../_code/cli.php cron delivery
#sync item
#for((i=0;i<3;i=i+1))
#do
#checkprocess ../_code/cli.php cron syncitem2 3,${i}
#done


# auto read updates log
#svn log .. -l100 --xml > log/versions.log