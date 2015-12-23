:: 动态建模生成文件清理脚本
::	注, 该文件需要放入 项目文件根目录下 /Admin/cmd/

@echo off
cd Dynamicconf/truncate/
set "root=%cd%"
set "action=%root%/action.log"

if EXIST "action.log" (

for /f "tokens=1" %%a in (%action%) do (
	call :removeaction %%a
	call :removemodel %%a
	call :removetpl %%a
	call :removedynamicconf %%a
)

:: 删除完成后，清除删除列表记录 
::	:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
::	测试时不需要删除删除记录，
::cd ../../Dynamicconf/truncate/
::del /q/f action.log
::	:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
echo 删除完成，清除删除列表记录
) else ( 
@echo 删除失败，不存在action删除列表记录 )
exit
:removeaction
cd ../../Lib/Action/
set "action=%1Action.class.php"
set "extendAction=%1ExtendAction.class.php"
if exist %action% (del /q/f %action%) else ( @echo 文件 %action% 不存在 >> ../../Dynamicconf/truncate/dels.log )
if exist %extendAction% (del /q/f %extendAction%) else ( @echo 文件 %extendAction% 不存在 >> ../../Dynamicconf/truncate/dels.log )
goto:eof
:removemodel
cd ../../Lib/Model/
set "model=%1Model.class.php"
set "extendModel=%1ExtendModel.class.php"
set "view=%1ViewModel.class.php"
if exist %model% (del /q/f %model%) else ( @echo 文件 %model% 不存在 >> ../../Dynamicconf/truncate/dels.log )
if exist %extendModel% (del /q/f %extendModel%) else ( @echo 文件 %extendModel% 不存在 >> ../../Dynamicconf/truncate/dels.log )
if exist %view% (del /q/f %view%) else ( @echo 文件 %view% 不存在 >> ../../Dynamicconf/truncate/dels.log )
goto:eof
:removetpl
cd ../../Tpl/default/
set "dir=%1"
if exist %dir% (rd /q/s %dir%) else ( @echo 模板 %dir% 不存在 >> ../../Dynamicconf/truncate/dels.log )
goto:eof
:removedynamicconf
cd ../../Dynamicconf/Models/
set "dir=%1"
if exist %dir% (rd /q/s %dir%) else ( @echo 表单配置 %dir% 不存在 >> ../../Dynamicconf/truncate/dels.log )
goto:eof
:logs
set "name=%1"
echo %name%
@echo %name% 不存在 >> ../../Dynamicconf/truncate/dels.log
goto:eof