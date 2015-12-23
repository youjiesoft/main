:: windows 定时任务创建

Schtasks /create /sc minute /mo 1 /tn "phptask" /tr %cd%\run.vbs