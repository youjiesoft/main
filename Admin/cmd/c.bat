@echo offset
@echo 收集文件名列表
:: 切换到真实目录
cd ../Lib/Action/
:: 清空现有内容

:: 抓取当前目录下的所有txt文件写入del.log文件中
for %%h in (MisAuto*Action.class.php) do (
echo %%h
set "str=MisAutoTraAction.class.php"
echo 第一个字符为：%str:~0,10%



echo %str:~0,10% >> action.log
pause && exit
)
@echo 收集完成
pause
exit