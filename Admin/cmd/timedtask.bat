:: windows 定时任务 任务脚本
set "phproot=E:\installdir\develop\wamp\bin\php\php5.3.10\php.exe"
set "codepath=E:\nbm\work\php\systemui\Admin\cmd\"
set "code=%codepath%\timedtask.php"
:: %phproot% %code%

:: CLI 命令行模式下的测试
set "phptestroot=E:\installdir\develop\wamp\bin\php\php5.3.10\php-cgi.exe"
set "testcode= -q E:\nbm\work\php\tp\Admin\index.php Index/index"
:: D:\php\php.exe -q D:\website\test.php
:: 调用测试 TP CLI 代码
%phptestroot%%testcode%
::pause
exit