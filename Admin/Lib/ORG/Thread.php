<?php
    /**
     * @title:      PHP多线程类(Thread)
     * @version:    1.0
     * @author:     phper.org.cn  web@phper.org.cn
     * @published:  2010-11-2
     *
     * PHP多线程应用示例：
     *  require_once 'thread.class.php';
     *  $thread = new thread();
     *  $thread->addthread('action_log','a');
     *  $thread->addthread('action_log','b');
     *  $thread->addthread('action_log','c');
     *  $thread->runthread();
     *
     *  function action_log($info) {
     *      $log = 'log/' . microtime() . '.log';
     *      $txt = $info . " " . 'Set in ' . Date('h:i:s', time()) . (double)microtime() . " ";
     *      $fp = fopen($log, 'w');
     *      fwrite($fp, $txt);
     *      fclose($fp);
     *  }
     *
     * EP
     *   $thread = new thread();
     *   $thread->addthread('func1','info1');
     *   $thread->addthread('func2','info2');
     *   $thread->addthread('func3','info3');
     *   $thread->runthread();
     *   说明：
     *   addthread是添加线程函数，第一个参数是函数名，之后的参数（可选）为传递给指定函数的参数。
     *   runthread是执行线程的函数。
     */
    class thread {

        var $hooks = array();
        var $args = array();

        function thread() {
        }

        function addthread($func)
        {
            $args = array_slice(func_get_args(), 1);
            $this->hooks[] = $func;
            $this->args[] = $args;
            return true;
        }

        function runthread()
        {
            if(isset($_GET['flag']))
            {
                $flag = intval($_GET['flag']);
            }
            if($flag || $flag === 0)
            {
                call_user_func_array($this->hooks[$flag], $this->args[$flag]);
            }
            else
            {
                for($i = 0, $size = count($this->hooks); $i < $size; $i++)
                {
                    $fp=fsockopen($_SERVER['HTTP_HOST'],$_SERVER['SERVER_PORT']);
                    if($fp)
                    {
                        $out = "GET {$_SERVER['PHP_SELF']}?flag=$i HTTP/1.1 ";
                        $out .= "Host: {$_SERVER['HTTP_HOST']} ";
                        $out .= "Connection: Close ";
                        fputs($fp,$out);
                        fclose($fp);
                    }
                }
            }
        }
    }

?>