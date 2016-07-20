<?php
function createFolders_upload($path) {
        if (!file_exists($path)) {
            createFolders_upload(dirname($path));
            @mkdir($path, 0777);
        }
}
if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	//print_r($_REQUEST['uploadpath']);
	//print_r($_SERVER['DOCUMENT_ROOT']);
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['uploadpath'] . '/'.date("Y/m/d/",time());
	$targetPath =  str_replace('//','/',$targetPath);
	if(!file_exists($targetPath)){
		createFolders_upload($targetPath);
	}
	$arr=explode(".",$_FILES['Filedata']['name']);
	$newname= rand("100","999").time().".".end($arr);
	$targetFile =  str_replace('//','/',$targetPath) . $newname;
	if(file_exists($targetFile)){
		unlink($targetFile);
	}
	$fileTypes = array('jpg', 'gif', 'png', 'jpeg','doc','xls','csv','zip','pdf','xlsx','ppt','docx','rar','html','htm','apk');
	if (in_array(strtolower(end($arr)),$fileTypes)) {
		$ret = move_uploaded_file($tempFile,$targetFile);
		
		/**
		*	图片压缩,按原始比例将文件体积改小
		*/
		if($ret){
			//list($width, $height) = getimagesize($targetFile);
			//imagezoom($targetFile, $targetFile, $width, $height, '#FFFFFF');
			// 图片压缩end
		}
		//print_r($targetFile);
		$s=explode($_REQUEST['uploadpath'],$targetFile);
// 		print_r($s);
		echo $_REQUEST['uploadpath'].$s[1];
	} else {
		echo 'Invalid file type.';
	}
}


/*
    php缩略图函数：
        等比例无损压缩，可填充补充色
    主持格式：
        bmp 、jpg 、gif、png
    param:
        @srcimage : 要缩小的图片
        @dstimage : 要保存的图片
        @dst_width: 缩小宽
        @dst_height: 缩小高
        @backgroundcolor: 补充色  如：#FFFFFF  支持 6位  不支持3位
*/
function imagezoom( $srcimage, $dstimage,  $dst_width, $dst_height, $backgroundcolor ) {
         
        // 中文件名乱码
        if ( PHP_OS == 'WINNT' ) {
                $srcimage = iconv('UTF-8', 'GBK', $srcimage);
                $dstimage = iconv('UTF-8', 'GBK', $dstimage);
        }
     
    $dstimg = imagecreatetruecolor( $dst_width, $dst_height );
    $color = imagecolorallocate($dstimg
        , hexdec(substr($backgroundcolor, 1, 2))
        , hexdec(substr($backgroundcolor, 3, 2))
        , hexdec(substr($backgroundcolor, 5, 2))
    );
    imagefill($dstimg, 0, 0, $color);
     
    if ( !$arr=getimagesize($srcimage) ) {
                echo "要生成缩略图的文件不存在";
                exit;
        }
         
    $src_width = $arr[0];
    $src_height = $arr[1];
    $srcimg = null;
    $method = getcreatemethod( $srcimage );
    if ( $method ) {
        eval( '$srcimg = ' . $method . ';' );
    }
     
    $dst_x = 0;
    $dst_y = 0;
    $dst_w = $dst_width;
    $dst_h = $dst_height;
    if ( ($dst_width / $dst_height - $src_width / $src_height) > 0 ) {
        $dst_w = $src_width * ( $dst_height / $src_height );
        $dst_x = ( $dst_width - $dst_w ) / 2;
    } elseif ( ($dst_width / $dst_height - $src_width / $src_height) < 0 ) {
        $dst_h = $src_height * ( $dst_width / $src_width );
        $dst_y = ( $dst_height - $dst_h ) / 2;
    }
 
    imagecopyresampled($dstimg, $srcimg, $dst_x
        , $dst_y, 0, 0, $dst_w, $dst_h, $src_width, $src_height);
     
    // 保存格式
    $arr = array(
        'jpg' => 'imagejpeg'
        , 'jpeg' => 'imagejpeg'
        , 'png' => 'imagepng'
        , 'gif' => 'imagegif'
        , 'bmp' => 'imagebmp'
    );
    $suffix = strtolower( array_pop(explode('.', $dstimage ) ) );
    if (!in_array($suffix, array_keys($arr)) ) {
        echo "保存的文件名错误";
        exit;
    } else {
        eval( $arr[$suffix] . '($dstimg, "'.$dstimage.'");' );
    }
     
    imagejpeg($dstimg, $dstimage);
     
    imagedestroy($dstimg);
    imagedestroy($srcimg);
     
}
 
 
function getcreatemethod( $file ) {
        $arr = array(
                '474946' => "imagecreatefromgif('$file')"
                , 'FFD8FF' => "imagecreatefromjpeg('$file')"
                , '424D' => "imagecreatefrombmp('$file')"
                , '89504E' => "imagecreatefrompng('$file')"
        );
        $fd = fopen( $file, "rb" );
        $data = fread( $fd, 3 );
         
        $data = str2hex( $data );
         
        if ( array_key_exists( $data, $arr ) ) {
                return $arr[$data];
        } elseif ( array_key_exists( substr($data, 0, 4), $arr ) ) {
                return $arr[substr($data, 0, 4)];
        } else {
                return false;
        }
}

function str2hex( $str ) {
        $ret = "";
         
        for( $i = 0; $i < strlen( $str ) ; $i++ ) {
                $ret .= ord($str[$i]) >= 16 ? strval( dechex( ord($str[$i]) ) )
                        : '0'. strval( dechex( ord($str[$i]) ) );
        }
         
        return strtoupper( $ret );
}

// BMP 创建函数  php本身无
function imagecreatefrombmp($filename)
{
   if (! $f1 = fopen($filename,"rb")) return FALSE;
 
   $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
   if ($FILE['file_type'] != 19778) return FALSE;
 
   $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
                 '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
                 '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
   $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
   if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
   $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
   $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
   $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
   $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
   $BMP['decal'] = 4-(4*$BMP['decal']);
   if ($BMP['decal'] == 4) $BMP['decal'] = 0;
    
   $PALETTE = array();
   if ($BMP['colors'] < 16777216)
   {
    $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
   }
    
   $IMG = fread($f1,$BMP['size_bitmap']);
   $VIDE = chr(0);
 
   $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
   $P = 0;
   $Y = $BMP['height']-1;
   while ($Y >= 0)
   {
        $X=0;
        while ($X < $BMP['width'])
        {
         if ($BMP['bits_per_pixel'] == 24)
            $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
         elseif ($BMP['bits_per_pixel'] == 16)
         {  
            $COLOR = unpack("n",substr($IMG,$P,2));
            $COLOR[1] = $PALETTE[$COLOR[1]+1];
         }
         elseif ($BMP['bits_per_pixel'] == 8)
         {  
            $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
            $COLOR[1] = $PALETTE[$COLOR[1]+1];
         }
         elseif ($BMP['bits_per_pixel'] == 4)
         {
            $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
            if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
            $COLOR[1] = $PALETTE[$COLOR[1]+1];
         }
         elseif ($BMP['bits_per_pixel'] == 1)
         {
            $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
            if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
            elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
            elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
            elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
            elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
            elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
            elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
            elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
            $COLOR[1] = $PALETTE[$COLOR[1]+1];
         }
         else
            return FALSE;
         imagesetpixel($res,$X,$Y,$COLOR[1]);
         $X++;
         $P += $BMP['bytes_per_pixel'];
        }
        $Y--;
        $P+=$BMP['decal'];
   }
   fclose($f1);
 
return $res;
}
// BMP 保存函数，php本身无
function imagebmp ($im, $fn = false)
{
    if (!$im) return false;
             
    if ($fn === false) $fn = 'php://output';
    $f = fopen ($fn, "w");
    if (!$f) return false;
             
    $biWidth = imagesx ($im);
    $biHeight = imagesy ($im);
    $biBPLine = $biWidth * 3;
    $biStride = ($biBPLine + 3) & ~3;
    $biSizeImage = $biStride * $biHeight;
    $bfOffBits = 54;
    $bfSize = $bfOffBits + $biSizeImage;
             
    fwrite ($f, 'BM', 2);
    fwrite ($f, pack ('VvvV', $bfSize, 0, 0, $bfOffBits));
             
    fwrite ($f, pack ('VVVvvVVVVVV', 40, $biWidth, $biHeight, 1, 24, 0, $biSizeImage, 0, 0, 0, 0));
             
    $numpad = $biStride - $biBPLine;
    for ($y = $biHeight - 1; $y >= 0; --$y)
    {
        for ($x = 0; $x < $biWidth; ++$x)
        {
            $col = imagecolorat ($im, $x, $y);
            fwrite ($f, pack ('V', $col), 3);
        }
        for ($i = 0; $i < $numpad; ++$i)
            fwrite ($f, pack ('C', 0));
    }
    fclose ($f);
    return true;
}
?>