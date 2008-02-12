<?php
/*
*------------------------------------------------------------
*                   ICO Image functions
*------------------------------------------------------------
*                      By JPEXS
*/

define("TRUE_COLOR", 16777216);
define("XP_COLOR", 4294967296);
define("MAX_COLOR", -2);
define("MAX_SIZE", -2);

/*
*------------------------------------------------------------
*                    ImageCreateFromIco
*------------------------------------------------------------
*            - Reads image from a ICO file
*
*         Parameters:  $filename - Target ico file to load
*                 $icoColorCount - Icon color count (For multiple icons ico file)
*                                - 2,16,256, TRUE_COLOR or XP_COLOR
*                       $icoSize - Icon width       (For multiple icons ico file)
*            Returns: Image ID
*/


function ImageCreateFromIco($filename,$icoColorCount=16,$icoSize=16)
{
$Ikona=GetIconsInfo($filename);

$IconID=-1;

$ColMax=-1;
$SizeMax=-1;

for($p=0;$p<count($Ikona);$p++)
{
$Ikona[$p]["NumberOfColors"]=pow(2,$Ikona[$p]["Info"]["BitsPerPixel"]);
};


for($p=0;$p<count($Ikona);$p++)
{

if(($ColMax==-1)or($Ikona[$p]["NumberOfColors"]>$Ikona[$ColMax]["NumberOfColors"]))
if(($icoSize==$Ikona[$p]["Width"])or($icoSize==-2))
 {
  $ColMax=$p;
 };

if(($SizeMax==-1)or($Ikona[$p]["Width"]>$Ikona[$SizeMax]["Width"]))
if(($icoColorCount==$Ikona[$p]["NumberOfColors"])or($icoColorCount==-2))
 {
   $SizeMax=$p;
 };


if($Ikona[$p]["NumberOfColors"]==$icoColorCount)
if($Ikona[$p]["Width"]==$icoSize)
 {

 $IconID=$p;
 };
};

if($icoSize==-2) $IconID=$SizeMax;
if($icoColorCount==-2) $IconID=$ColMax;

$ColName=$icoColorCount;

if($icoSize==-2) $icoSize="Max";
if($ColName==16777216) $ColName="True";
if($ColName==4294967296) $ColName="XP";
if($ColName==-2) $ColName="Max";
if($IconID==-1) die("Icon with $ColName colors and $icoSize x $icoSize size doesn't exist in this file!");


ReadIcon($filename,$IconID,$Ikona);

 $biBitCount=$Ikona[$IconID]["Info"]["BitsPerPixel"];


  if($Ikona[$IconID]["Info"]["BitsPerPixel"]==0)
  {
  $Ikona[$IconID]["Info"]["BitsPerPixel"]=24;
  };

 $biBitCount=$Ikona[$IconID]["Info"]["BitsPerPixel"];
 if($biBitCount==0) $biBitCount=1;


$Ikona[$IconID]["BitCount"]=$Ikona[$IconID]["Info"]["BitsPerPixel"];


if($Ikona[$IconID]["BitCount"]>=24)
{
$img=imagecreatetruecolor($Ikona[$IconID]["Width"],$Ikona[$IconID]["Height"]);

for($y=0;$y<$Ikona[$IconID]["Height"];$y++)
for($x=0;$x<$Ikona[$IconID]["Width"];$x++)
 {
 $R=$Ikona[$IconID]["Data"][$x][$y]["r"];
 $G=$Ikona[$IconID]["Data"][$x][$y]["g"];
 $B=$Ikona[$IconID]["Data"][$x][$y]["b"];
 $Alpha=round($Ikona[$IconID]["Data"][$x][$y]["alpha"]/2);

 if($Ikona[$IconID]["BitCount"]==32)
 {
 $color=imagecolorexactalpha($img,$R,$G,$B,$Alpha);
 if($color==-1) $color=imagecolorallocatealpha($img,$R,$G,$B,$Alpha);
 }
 else
 {
 $color=imagecolorexact($img,$R,$G,$B);
 if($color==-1) $color=imagecolorallocate($img,$R,$G,$B);
 };

 imagesetpixel($img,$x,$y,$color);

 };

}
else
{
$img=imagecreate($Ikona[$IconID]["Width"],$Ikona[$IconID]["Height"]);
for($p=0;$p<count($Ikona[$IconID]["Paleta"]);$p++)
 $Paleta[$p]=imagecolorallocate($img,$Ikona[$IconID]["Paleta"][$p]["r"],$Ikona[$IconID]["Paleta"][$p]["g"],$Ikona[$IconID]["Paleta"][$p]["b"]);

for($y=0;$y<$Ikona[$IconID]["Height"];$y++)
for($x=0;$x<$Ikona[$IconID]["Width"];$x++)
 {
 imagesetpixel($img,$x,$y,$Paleta[$Ikona[$IconID]["Data"][$x][$y]]);
 };
};





for($y=0;$y<$Ikona[$IconID]["Height"];$y++)
for($x=0;$x<$Ikona[$IconID]["Width"];$x++)
 if($Ikona[$IconID]["Maska"][$x][$y]==1)
  {
   $IsTransparent=true;
   break;
  };
if($Ikona[$IconID]["BitCount"]==32)
{
 imagealphablending($img, FALSE);
 if(function_exists("imagesavealpha"))
  imagesavealpha($img,true);
};

 if($IsTransparent)
 {
  if(($Ikona[$IconID]["BitCount"]>=24)or(imagecolorstotal($img)>=256))
   {
   $img2=imagecreatetruecolor(imagesx($img),imagesy($img));
   imagecopy($img2,$img,0,0,0,0,imagesx($img),imagesy($img));
   imagedestroy($img);
   $img=$img2;
   imagetruecolortopalette($img,true,255);

   };
    $Pruhledna=imagecolorallocate($img,0,0,0);
    for($y=0;$y<$Ikona[$IconID]["Height"];$y++)
     for($x=0;$x<$Ikona[$IconID]["Width"];$x++)
      if($Ikona[$IconID]["Maska"][$x][$y]==1)
       {
        imagesetpixel($img,$x,$y,$Pruhledna);
       };
  imagecolortransparent($img,$Pruhledna);
 };

return $img;


};




function ReadIcon($filename,$id,&$Ikona)
{
global $CurrentBit;

$f=fopen($filename,"rb");

fseek($f,6+$id*16);
  $Width=freadbyte($f);
  $Height=freadbyte($f);
fseek($f,6+$id*16+12);
$OffSet=freaddword($f);
fseek($f,$OffSet);

$p=$id;

  $Ikona[$p]["Info"]["HeaderSize"]=freadlngint($f);
  $Ikona[$p]["Info"]["ImageWidth"]=freadlngint($f);
  $Ikona[$p]["Info"]["ImageHeight"]=freadlngint($f);
  $Ikona[$p]["Info"]["NumberOfImagePlanes"]=freadword($f);
  $Ikona[$p]["Info"]["BitsPerPixel"]=freadword($f);
  $Ikona[$p]["Info"]["CompressionMethod"]=freadlngint($f);
  $Ikona[$p]["Info"]["SizeOfBitmap"]=freadlngint($f);
  $Ikona[$p]["Info"]["HorzResolution"]=freadlngint($f);
  $Ikona[$p]["Info"]["VertResolution"]=freadlngint($f);
  $Ikona[$p]["Info"]["NumColorUsed"]=freadlngint($f);
  $Ikona[$p]["Info"]["NumSignificantColors"]=freadlngint($f);


 $biBitCount=$Ikona[$p]["Info"]["BitsPerPixel"];

 if($Ikona[$p]["Info"]["BitsPerPixel"]<=8)
  {

 $barev=pow(2,$biBitCount);

  for($b=0;$b<$barev;$b++)
    {
    $Ikona[$p]["Paleta"][$b]["b"]=freadbyte($f);
    $Ikona[$p]["Paleta"][$b]["g"]=freadbyte($f);
    $Ikona[$p]["Paleta"][$b]["r"]=freadbyte($f);
    freadbyte($f);
    };

$Zbytek=(4-ceil(($Width/(8/$biBitCount)))%4)%4;


for($y=$Height-1;$y>=0;$y--)
    {
     $CurrentBit=0;
     for($x=0;$x<$Width;$x++)
      {
         $C=freadbits($f,$biBitCount);
         $Ikona[$p]["Data"][$x][$y]=$C;
      };

    if($CurrentBit!=0) {freadbyte($f);};
    for($g=0;$g<$Zbytek;$g++)
     freadbyte($f);
     };

}
elseif($biBitCount==24)
{
 $Zbytek=$Width%4;

   for($y=$Height-1;$y>=0;$y--)
    {
     for($x=0;$x<$Width;$x++)
      {
       $B=freadbyte($f);
       $G=freadbyte($f);
       $R=freadbyte($f);
       $Ikona[$p]["Data"][$x][$y]["r"]=$R;
       $Ikona[$p]["Data"][$x][$y]["g"]=$G;
       $Ikona[$p]["Data"][$x][$y]["b"]=$B;
      }
    for($z=0;$z<$Zbytek;$z++)
     freadbyte($f);
   };
}
elseif($biBitCount==32)
{
 $Zbytek=$Width%4;

   for($y=$Height-1;$y>=0;$y--)
    {
     for($x=0;$x<$Width;$x++)
      {
       $B=freadbyte($f);
       $G=freadbyte($f);
       $R=freadbyte($f);
       $Alpha=freadbyte($f);
       $Ikona[$p]["Data"][$x][$y]["r"]=$R;
       $Ikona[$p]["Data"][$x][$y]["g"]=$G;
       $Ikona[$p]["Data"][$x][$y]["b"]=$B;
       $Ikona[$p]["Data"][$x][$y]["alpha"]=$Alpha;
      }
    for($z=0;$z<$Zbytek;$z++)
     freadbyte($f);
   };
};


//Maska
$Zbytek=(4-ceil(($Width/(8)))%4)%4;
for($y=$Height-1;$y>=0;$y--)
    {
     $CurrentBit=0;
     for($x=0;$x<$Width;$x++)
      {
         $C=freadbits($f,1);
         $Ikona[$p]["Maska"][$x][$y]=$C;
      };
    if($CurrentBit!=0) {freadbyte($f);};
    for($g=0;$g<$Zbytek;$g++)
     freadbyte($f);
     };
//--------------

fclose($f);

};

function GetIconsInfo($filename)
{
global $CurrentBit;

$f=fopen($filename,"rb");

$Reserved=freadword($f);
$Type=freadword($f);
$Count=freadword($f);
for($p=0;$p<$Count;$p++)
 {
  $Ikona[$p]["Width"]=freadbyte($f);
  $Ikona[$p]["Height"]=freadbyte($f);
  $Ikona[$p]["ColorCount"]=freadword($f);
 if($Ikona[$p]["ColorCount"]==0) $Ikona[$p]["ColorCount"]=256;
  $Ikona[$p]["Planes"]=freadword($f);
  $Ikona[$p]["BitCount"]=freadword($f);
  $Ikona[$p]["BytesInRes"]=freaddword($f);
  $Ikona[$p]["ImageOffset"]=freaddword($f);
 };

for($p=0;$p<$Count;$p++)
 {
  fseek($f,$Ikona[$p]["ImageOffset"]+14);
  $Ikona[$p]["Info"]["BitsPerPixel"]=freadword($f);
 };

fclose($f);
return $Ikona;
};



/*
*------------------------------------------------------------
*                       ImageIco
*------------------------------------------------------------
*                 - Returns ICO file
*
*         Parameters:       $img - Target Image (Can be array of images)
*                      $filename - Target ico file to save
*
*
* Note: For returning icons to Browser, you have to set header:
*
*             header("Content-type: image/x-icon");
*
*/


function ImageIco($Images/*image or image array*/,$filename="")
{

if(is_array($Images))
{
$ImageCount=count($Images);
$Image=$Images;
}
else
{
$Image[0]=$Images;
$ImageCount=1;
};


$WriteToFile=false;

if($filename!="")
{
$WriteToFile=true;
};


$ret="";

$ret.=inttoword(0); //PASSWORD
$ret.=inttoword(1); //SOURCE
$ret.=inttoword($ImageCount); //ICONCOUNT


for($q=0;$q<$ImageCount;$q++)
{
$img=$Image[$q];

$Width=imagesx($img);
$Height=imagesy($img);

$ColorCount=imagecolorstotal($img);

$Transparent=imagecolortransparent($img);
$IsTransparent=$Transparent!=-1;


if($IsTransparent) $ColorCount--;

if($ColorCount==0) {$ColorCount=0; $BitCount=24;};
if(($ColorCount>0)and($ColorCount<=2)) {$ColorCount=2; $BitCount=1;};
if(($ColorCount>2)and($ColorCount<=16)) { $ColorCount=16; $BitCount=4;};
if(($ColorCount>16)and($ColorCount<=256)) { $ColorCount=0; $BitCount=8;};





//ICONINFO:
$ret.=inttobyte($Width);//
$ret.=inttobyte($Height);//
$ret.=inttobyte($ColorCount);//
$ret.=inttobyte(0);//RESERVED

$Planes=0;
if($BitCount>=8) $Planes=1;

$ret.=inttoword($f,$Planes);//PLANES
if($BitCount>=8) $WBitCount=$BitCount;
if($BitCount==4) $WBitCount=0;
if($BitCount==1) $WBitCount=0;
$ret.=inttoword($WBitCount);//BITS

$Zbytek=(4-($Width/(8/$BitCount))%4)%4;
$ZbytekMask=(4-($Width/8)%4)%4;

$PalSize=0;

$Size=40+($Width/(8/$BitCount)+$Zbytek)*$Height+(($Width/8+$ZbytekMask) * $Height);
if($BitCount<24)
 $Size+=pow(2,$BitCount)*4;
$IconId=1;
$ret.=inttodword($Size); //SIZE
$OffSet=6+16*$ImageCount+$FullSize;
$ret.=inttodword(6+16*$ImageCount+$FullSize);//OFFSET
$FullSize+=$Size;
//-------------

};


for($q=0;$q<$ImageCount;$q++)
{
$img=$Image[$q];
$Width=imagesx($img);
$Height=imagesy($img);
$ColorCount=imagecolorstotal($img);

$Transparent=imagecolortransparent($img);
$IsTransparent=$Transparent!=-1;

if($IsTransparent) $ColorCount--;
if($ColorCount==0) {$ColorCount=0; $BitCount=24;};
if(($ColorCount>0)and($ColorCount<=2)) {$ColorCount=2; $BitCount=1;};
if(($ColorCount>2)and($ColorCount<=16)) { $ColorCount=16; $BitCount=4;};
if(($ColorCount>16)and($ColorCount<=256)) { $ColorCount=0; $BitCount=8;};



//ICONS
$ret.=inttodword(40);//HEADSIZE
$ret.=inttodword($Width);//
$ret.=inttodword(2*$Height);//
$ret.=inttoword(1); //PLANES
$ret.=inttoword($BitCount);   //
$ret.=inttodword(0);//Compress method


$ZbytekMask=($Width/8)%4;

$Zbytek=($Width/(8/$BitCount))%4;
$Size=($Width/(8/$BitCount)+$Zbytek)*$Height+(($Width/8+$ZbytekMask) * $Height);

$ret.=inttodword($Size);//SIZE

$ret.=inttodword(0);//HPIXEL_M
$ret.=inttodword(0);//V_PIXEL_M
$ret.=inttodword($ColorCount); //UCOLORS
$ret.=inttodword(0); //DCOLORS
//---------------


$CC=$ColorCount;
if($CC==0) $CC=256;

if($BitCount<24)
{
 $ColorTotal=imagecolorstotal($img);
 if($IsTransparent) $ColorTotal--;

 for($p=0;$p<$ColorTotal;$p++)
  {
   $color=imagecolorsforindex($img,$p);
   $ret.=inttobyte($color["blue"]);
   $ret.=inttobyte($color["green"]);
   $ret.=inttobyte($color["red"]);
   $ret.=inttobyte(0); //RESERVED
  };

 $CT=$ColorTotal;
 for($p=$ColorTotal;$p<$CC;$p++)
  {
   $ret.=inttobyte(0);
   $ret.=inttobyte(0);
   $ret.=inttobyte(0);
   $ret.=inttobyte(0); //RESERVED
  };
};






if($BitCount<=8)
{

 for($y=$Height-1;$y>=0;$y--)
 {
  $bWrite="";
  for($x=0;$x<$Width;$x++)
   {
   $color=imagecolorat($img,$x,$y);
   if($color==$Transparent)
    $color=imagecolorexact($img,0,0,0);
   if($color==-1) $color=0;
   if($color>pow(2,$BitCount)-1) $color=0;

   $bWrite.=decbinx($color,$BitCount);
   if(strlen($bWrite)==8)
    {
     $ret.=inttobyte(bindec($bWrite));
     $bWrite="";
    };
   };

  if((strlen($bWrite)<8)and(strlen($bWrite)!=0))
    {
     $sl=strlen($bWrite);
     for($t=0;$t<8-$sl;$t++)
      $sl.="0";
     $ret.=inttobyte(bindec($bWrite));
    };
  for($z=0;$z<$Zbytek;$z++)
   $ret.=inttobyte(0);
 };
};



if($BitCount>=24)
{
 for($y=$Height-1;$y>=0;$y--)
 {
  for($x=0;$x<$Width;$x++)
   {
   $color=imagecolorsforindex($img,imagecolorat($img,$x,$y));
   $ret.=inttobyte($color["blue"]);
   $ret.=inttobyte($color["green"]);
   $ret.=inttobyte($color["red"]);
   if($BitCount==32)
    $ret.=inttobyte(0);//Alpha for XP_COLORS
   };
  for($z=0;$z<$Zbytek;$z++)
   $ret.=inttobyte(0);
 };
};


//MASK

 for($y=$Height-1;$y>=0;$y--)
 {
  $byteCount=0;
  $bOut="";
  for($x=0;$x<$Width;$x++)
   {
    if(($Transparent!=-1)and(imagecolorat($img,$x,$y)==$Transparent))
     {
      $bOut.="1";
     }
     else
     {
      $bOut.="0";
     };
   };
  for($p=0;$p<strlen($bOut);$p+=8)
  {
   $byte=bindec(substr($bOut,$p,8));
   $byteCount++;
   $ret.=inttobyte($byte);
  // echo dechex($byte)." ";
  };
 $Zbytek=$byteCount%4;
  for($z=0;$z<$Zbytek;$z++)
   {
   $ret.=inttobyte(0xff);
  // echo "FF ";
   };
 };

//------------------

};//q





if($WriteToFile)
{
 $f=fopen($filename,"w");
 fwrite($f,$ret);
 fclose($f);
}
else
{
 echo $ret;
};

};




/*
* Helping functions:
*-------------------------
*
* inttobyte($n) - returns chr(n)
* inttodword($n) - returns dword (n)
* inttoword($n) - returns word(n)
* freadbyte($file) - reads 1 byte from $file
* freadword($file) - reads 2 bytes (1 word) from $file
* freaddword($file) - reads 4 bytes (1 dword) from $file
* freadlngint($file) - same as freaddword($file)
* decbin8($d) - returns binary string of d zero filled to 8
* RetBits($byte,$start,$len) - returns bits $start->$start+$len from $byte
* freadbits($file,$count) - reads next $count bits from $file
*/

function decbin8($d)
{
return decbinx($d,8);
};

function decbinx($d,$n)
{
$bin=decbin($d);
$sbin=strlen($bin);
for($j=0;$j<$n-$sbin;$j++)
 $bin="0$bin";
return $bin;
};

function RetBits($byte,$start,$len)
{
$bin=decbin8($byte);
$r=bindec(substr($bin,$start,$len));
return $r;

};



$CurrentBit=0;
function freadbits($f,$count)
{
 global $CurrentBit,$SMode;
 $Byte=freadbyte($f);
 $LastCBit=$CurrentBit;
 $CurrentBit+=$count;
 if($CurrentBit==8)
  {
   $CurrentBit=0;
  }
 else
  {
   fseek($f,ftell($f)-1);
  };
 return RetBits($Byte,$LastCBit,$count);
};


function freadbyte($f)
{
 return ord(fread($f,1));
};

function freadword($f)
{
 $b1=freadbyte($f);
 $b2=freadbyte($f);
 return $b2*256+$b1;
};


function freadlngint($f)
{
return freaddword($f);
};

function freaddword($f)
{
 $b1=freadword($f);
 $b2=freadword($f);
 return $b2*65536+$b1;
};

function inttobyte($n)
{
return chr($n);
};

function inttodword($n)
{
return chr($n & 255).chr(($n >> 8) & 255).chr(($n >> 16) & 255).chr(($n >> 24) & 255);
};

function inttoword($n)
 {
 return chr($n & 255).chr(($n >> 8) & 255);
 };



?>