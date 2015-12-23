<?php

require('Calendar.class.php'); 

class Gantti {

  var $cal           = null;
  var $data          = array();
  var $first         = false;
  var $last          = false;
  var $options       = array();
  var $cellstyle     = false;
  var $blocks        = array();
  var $months        = array();
  var $days          = array();
  var $seconds       = 0;
  

  var $datalenth     = 0;//$data数据长度
  var $apendlenth    =0;//拼接长度

  function __construct($data, $params=array()) {
    
    $defaults = array(
      'title'      => false,
      'cellwidth'  => 40,
      'cellheight' => 40,
      'today'      => true,
      'language'   => 'EN',
      'lenth'      => 7,
      'isdoubleline' =>false,//是否预期，实际同时显示双行
      'plan'       =>'预期',
      'reality'    =>'实际',
    );
    
    if ($data and is_array($data)) {
        $this->datalenth = count($data);
    } else {
        $this->datalenth = 0;
    }
    
    
    
    $this->options = array_merge($defaults, $params);
    $this->apendlenth = $this->options['lenth'] - $this->datalenth;
    $this->cal     = new Calendar();
    $this->data    = $data;
    $this->seconds = 60*60*24;

    $this->cellstyle = 'style="width: ' . $this->options['cellwidth'] . 'px; height: ' . $this->options['cellheight'] . 'px"';
    
    // parse data and find first and last date  
    $this->parse();                
                    
  }

  function parse() {
    
    foreach($this->data as $d) {
              
      $this->blocks[] = array(
        'label' => $d['label'],
        'start' => $start = strtotime($d['start']),
        'end'   => $end   = strtotime($d['end']),
        //显示日期横线条变量
        'dateshow'   => $d['dateshow'] === false ? $d['dateshow'] : true ,
        'class' => @$d['class']
      );
      
      if(!$this->first || $this->first > $start) $this->first = $start;
      if(!$this->last  || $this->last  < $end)   $this->last  = $end;
          
    }
    
    $this->first = $this->cal->date($this->first);
    $this->last  = $this->cal->date($this->last);

    $current = $this->first->month();
    $lastDay = $this->last->month()->lastDay()->timestamp;

    // build the months      
    while($current->lastDay()->timestamp <= $lastDay) {
      $month = $current->month();
      $this->months[] = $month;
      foreach($month->days() as $day) {
        $this->days[] = $day;
      }
      $current = $current->next();
    }
        
  }

  function render() {
    
    $html = array();
    
    // common styles    
    $cellstyle  = 'style="line-height: ' . $this->options['cellheight'] . 'px; height: ' . $this->options['cellheight'] . 'px"';
    $wrapstyle  = 'style="width: ' . $this->options['cellwidth'] . 'px"';
    $totalstyle = 'style="width: ' . ((count($this->days)-strftime('%d',$this->first->timestamp)-28+strftime('%d',$this->last->timestamp))*$this->options['cellwidth']) . 'px"';
    // start the diagram    
    $html[] = '<figure class="gantt">';    

    // set a title if available
    if($this->options['title']) {
      $html[] = '<figcaption>' . $this->options['title'] . '</figcaption>';
    }

    // sidebar with labels
    $html[] = '<aside>';
    $html[] = '<ul class="gantt-labels" style="margin-top: ' . (($this->options['cellheight']*2)+1) . 'px">';
    foreach($this->blocks as $i => $block) {
      
      if ($this->options['isdoubleline']) {
        if (0 === $i%2) {
          
          $html[] = '<li class="gantt-label"><strong  style="line-height: ' . $this->options['cellheight'] . 'px; height: ' . ($this->options['cellheight'] * 2+1) . 'px">' . $block['label'] . '</strong></li>';  
        } 
      } else {
        $html[] = '<li class="gantt-label"><strong ' . $cellstyle . '>' . $block['label'] . '</strong></li>';
      }
      
    }
    
    //拼接空白列，列数少时增加篇幅
    //begin
    for($i=0; $i < $this->apendlenth; $i++){
        $html[] = '<li class="gantt-label"><strong ' . $cellstyle . '></strong></li>';  
    }
    //end
    
    $html[] = '</ul>';
    $html[] = '</aside>';

    // data section
    $html[] = '<section class="gantt-data">';
        
    // data header section
    $html[] = '<header>';

    // months headers
    $html[] = '<ul class="gantt-months" ' . $totalstyle . '>';
    foreach($this->months as $key=>$month) {
      if ($key == 0) {
        $html[] = '<li class="gantt-month" style="width: ' . ($this->options['cellwidth'] * ($month->countDays()-strftime('%d', $this->first->timestamp)+1)) . 'px"><strong ' . $cellstyle . '>' . $month->name($this->options['language']) . '</strong></li>';
      } elseif (($key+1) == count($this->months)) {
        $html[] = '<li class="gantt-month" style="width: ' . ($this->options['cellwidth'] * strftime('%d', $this->last->timestamp)) . 'px"><strong ' . $cellstyle . '>' . $month->name($this->options['language']) . '</strong></li>';
      }
      else {
        $html[] = '<li class="gantt-month" style="width: ' . ($this->options['cellwidth'] * $month->countDays()) . 'px"><strong ' . $cellstyle . '>' . $month->name($this->options['language']) . '</strong></li>';
      }
    }
    $html[] = '</ul>';    

    // days headers
    $html[] = '<ul class="gantt-days" ' . $totalstyle . '>';
    //日期数字
    foreach($this->days as $day) {
      $weekend = ($day->isWeekend()) ? ' weekend' : '';
      $today   = ($day->isToday())   ? ' today' : '';
      if ($day->timestamp >= $this->first->timestamp and $day->timestamp <= $this->last->timestamp) {
        $html[] = '<li class="gantt-day' . $weekend . $today . '" ' . $wrapstyle . '><span ' . $cellstyle . '>' . $day->padded() . '</span></li>';
      }
      
    }                      
    $html[] = '</ul>';    
    
    // end header
    $html[] = '</header>';

    // main items
    $html[] = '<ul class="gantt-items" ' . $totalstyle . '>';
    foreach($this->blocks as $i => $block) {
      $html[] = '<li class="gantt-item">';
      
      // days
      $html[] = '<ul class="gantt-days">';
      foreach($this->days as $day) {
        
        $weekend = ($day->isWeekend()) ? ' weekend' : '';
        $today   = ($day->isToday())   ? ' today' : '';
        if ($day->timestamp >= $this->first->timestamp and $day->timestamp <= $this->last->timestamp) {
          $html[] = '<li class="gantt-day' . $weekend . $today . '" ' . $wrapstyle . '><span ' . $cellstyle . '></span></li>';
        }
        
      }    
      $html[] = '</ul>';    

      // the block
      $days   = (($block['end'] - $block['start']) / $this->seconds)+1;
      
      $offset = (($block['start'] - $this->first->month()->timestamp) / $this->seconds)-(strftime('%d', $this->first->timestamp)-1);
      //$offset = (($block['start'] - $this->first->month()->timestamp) / $this->seconds);
      $top    = round($i * ($this->options['cellheight'] + 1));
      $left   = round($offset * $this->options['cellwidth']);
      $width  = round($days * $this->options['cellwidth'] - 9);
      $height = round($this->options['cellheight']-8);
      $class  = ($block['class']) ? ' ' . $block['class'] : '';
      
      //判断//显示日期横线条
      if ($block['dateshow']) {
        if ($this->options['isdoubleline']) {
          $showtitle = $i%2 === 0 ? $this->options['plan'] : $this->options['reality'];
          $html[] = '<span class="gantt-block' . $class . '" style="left: ' . $left . 'px; width: ' . $width . 'px; height: ' . $height . 'px"><strong title="'.$showtitle.'" class="gantt-block-label">' . $days . '</strong></span>';
        } else {
          $html[] = '<span class="gantt-block' . $class . '" style="left: ' . $left . 'px; width: ' . $width . 'px; height: ' . $height . 'px"><strong  class="gantt-block-label">' . $days . '天</strong></span>';
        }
      }
     
      $html[] = '</li>';
    
    }
    //拼接空白列，列数少时增加篇幅
    //begin
     for($i=0; $i<$this->apendlenth; $i++){
        $html[] = '<li class="gantt-item">';
        $html[] = '<ul class="gantt-days">';
        foreach($this->days as $day) {
        
        $weekend = ($day->isWeekend()) ? ' weekend' : '';
        $today   = ($day->isToday())   ? ' today' : '';
        if ($day->timestamp >= $this->first->timestamp and $day->timestamp <= $this->last->timestamp) {
          $html[] = '<li class="gantt-day' . $weekend . $today . '" ' . $wrapstyle . '><span ' . $cellstyle . '></span></li>';
        }
        
      }    
      $html[] = '</ul>';
      $html[] = '</li>';
    }
    //end
    
    $html[] = '</ul>';    
    
    if($this->options['today']) {
    
      // today
      $today  = $this->cal->today();
      $offset = (($today->timestamp - $this->first->timestamp) / $this->seconds);
      //$offset = (($block['start'] - $this->first->month()->timestamp) / $this->seconds);
      //$offset = (($today->timestamp - $this->first->month()->timestamp) / $this->seconds)-(strftime('%d', $this->first->timestamp)-1);
      $left   = round($offset * $this->options['cellwidth']) + round(($this->options['cellwidth'] / 2) - 1);
          
      if($today->timestamp > $this->first->month()->firstDay()->timestamp && $today->timestamp < $this->last->month()->lastDay()->timestamp) {
        $html[] = '<time style="top: ' . ($this->options['cellheight'] * 2) . 'px; left: ' . $left . 'px" datetime="' . $today->format('Y-m-d') . '">Today</time>';
      }

    }
    
    // end data section
    $html[] = '</section>';    

    // end diagram
    $html[] = '</figure>';
    
   
    
    return implode('', $html);
      
  }
  
  function __toString() {
    return $this->render();
  }

}
