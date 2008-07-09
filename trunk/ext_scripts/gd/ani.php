<?php

/*
*------------------------------------------------------------
*                    ImageCreateFromAni
*------------------------------------------------------------
*            - Reads image from a ANI file
*
*         Parameters:  $filename - Target cur file to load
*                      $imageid - Specifies Image ID
*/

function imagecreatefromani($filename,$imageid)
{

$Info=ReadAniInfo($filename);

$f=fopen($filename,"r");

fseek($f,$Info["Icon"][$imageid]);

$IconSize=freaddword($f);
$Reserved=freadword($f);
$Type=freadword($f);
$Count=freadword($f);

  $Ikona["Width"]=freadbyte($f);
  $Ikona["Height"]=freadbyte($f);
  $Ikona["ColorCount"]=freadword($f);
 if($Ikona["ColorCount"]==0) $Ikona["ColorCount"]=256;
  $Ikona["Planes"]=freadword($f);
  $Ikona["BitCount"]=freadword($f);
  $Ikona["BytesInRes"]=freaddword($f);
  $Ikona["ImageOffset"]=freaddword($f);

  $Ikona["Info"]["HeaderSize"]=freadlngint($f);
  $Ikona["Info"]["ImageWidth"]=freadlngint($f);
  $Ikona["Info"]["ImageHeight"]=freadlngint($f);
  $Ikona["Info"]["NumberOfImagePlanes"]=freadword($f);
  $Ikona["Info"]["BitsPerPixel"]=freadword($f);
  $Ikona["Info"]["CompressionMethod"]=freadlngint($f);
  $Ikona["Info"]["SizeOfBitmap"]=freadlngint($f);
  $Ikona["Info"]["HorzResolution"]=freadlngint($f);
  $Ikona["Info"]["VertResolution"]=freadlngint($f);
  $Ikona["Info"]["NumColorUsed"]=freadlngint($f);
  $Ikona["Info"]["NumSignificantColors"]=freadlngint($f);

  $biBitCount=$Ikona["Info"]["BitsPerPixel"];

$Width=$Ikona["Width"];
$Height=$Ikona["Height"];

$img=imagecreatetruecolor($Ikona["Width"],$Ikona["Height"]);



if($biBitCount<=8)
  {

 $barev=pow(2,$biBitCount);

  for($b=0;$b<$barev;$b++)
    {
    $B=freadbyte($f);
    $G=freadbyte($f);
    $R=freadbyte($f);
    $Palette[]=imagecolorallocate($img,$R,$G,$B);
    freadbyte($f);
    };

$Zbytek=(4-ceil(($Width/(8/$biBitCount)))%4)%4;


for($y=$Height-1;$y>=0;$y--)
    {
     $CurrentBit=0;
     for($x=0;$x<$Width;$x++)
      {
         $C=freadbits($f,$biBitCount);
         imagesetpixel($img,$x,$y,$Palette[$C]);
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
      $color=imagecolorexact($img,$R,$G,$B);
      if($color==-1) $color=imagecolorallocate($img,$R,$G,$B);
      imagesetpixel($img,$x,$y,$color);
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
       $color=imagecolorexactalpha($img,$R,$G,$B,$Alpha);
       if($color==-1) $color=imagecolorallocatealpha($img,$R,$G,$B,$Alpha);
      imagesetpixel($img,$x,$y,$color);
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
         if($C==1)
          {
           if(!$IsTransparent)
{

            if(($biBitCount>=24)or(imagecolorstotal($img)>=256)or(imagecolorstotal($img)==0))
   {
   $img2=imagecreatetruecolor(imagesx($img),imagesy($img));
   imagecopy($img2,$img,0,0,0,0,imagesx($img),imagesy($img));
   imagedestroy($img);
   $img=$img2;

   imagetruecolortopalette($img,true,255);
   };
    $Pruhledna=imagecolorallocate($img,0,0,0);
};




           $IsTransparent=true;
           imagesetpixel($img,$x,$y,$Pruhledna);
          };
      };
    if($CurrentBit!=0) {freadbyte($f);};
    for($g=0;$g<$Zbytek;$g++)
     freadbyte($f);
     };


if($IsTransparent)
 imagecolortransparent($img,$Pruhledna);

fclose($f);

return $img;

};



function ReadAniInfo($filename)
{
$f=fopen($filename,"r");

$RIFF=fread($f,4);
if($RIFF=="RIFF")
{
 $Info["LengthOfFile"]=freaddword($f);

 while(!feof($f))
 {
 $PARAMETER=fread($f,4);
 if($PARAMETER=="LIST")
  $Info["LengthOfList"]=freaddword($f);
 if($PARAMETER=="INAM")
  {
  $LengthOfTitle=freaddword($f);
  $Info["Title"]=fread($f,$LengthOfTitle);
  };
 if($PARAMETER=="IART")
  {
  $LengthOfAuthor=freaddword($f);
  $Info["Author"]=fread($f,$LengthOfAuthor);
  };
 if($PARAMETER=="icon")
  {
  $Info["Icon"][]=ftell($f);
  $LengthOfIcon=freaddword($f);
  fseek($f,ftell($f)+$LengthOfIcon);
  };
 if($PARAMETER=="anih")
  {
  $Info["SizeOfAniHeader1"]=freaddword($f);
  $Info["SizeOfAniHeader2"]=freaddword($f);
  $Info["cFrames"]=freaddword($f);
  $Info["cSteps"]=freaddword($f);
  $Info["cX"]=freaddword($f);
  $Info["cY"]=freaddword($f);
  $Info["cBitCount"]=freaddword($f);
  $Info["cPlanes"]=freaddword($f);
  $Info["JifRate"]=freaddword($f);
  $Info["Flags"]=freaddword($f);
  };
 if($PARAMETER=="rate")
  {
  $LengthOfRate=freaddword($f);
  //$Info["Seq"]=fread($f,$LengthOfRate);
  for($p=0;$p<$Info["cSteps"];$p++)
   $Info["Rates"][]=freaddword($f);
  };
 if($PARAMETER=="seq ")
  {
  $LengthOfSeq=freaddword($f);
  for($p=0;$p<$Info["cSteps"];$p++)
   $Info["Seq"][]=freaddword($f);

  //$Info["Seq"]=fread($f,$LengthOfSeq);
  };
 };
};

fclose($f);
return $Info;
};


/*
*------------------------------------------------------------
*                       ImageAni
*------------------------------------------------------------
*                 - Returns ANI file
*
*         Parameters:       $img - Target Image (Can be array of images)
*                      $filename - Target ani file to save
*             $Rates - jifs rate of the images (OPTIONAL)
*                - can be array of integer if not array,
*                   use this value for all images
*             $Athor - Author name (OPTIONAL)
*             $Title - Title of the ani file (OPTIONAL)
*
*
*
*/

function ImageAni($Images,$filename="",$Rates=10,$Author="",$Title="")
{

if(is_array($Images))
{
 $Image=$Images;
}
else
{
 $Image[0]=$Images;
};


$OneRate=false;

if(!is_array($Rates))
{
 $Rate=$Rates;
 $DefaultRate=$Rate;
 unset($Rates);
 for($p=0;$p<count($Image);$p++)
  $Rates[$p]=$Rate;
 $OneRate=true;
};


$ANI.="ACON";

$anih=inttodword(36);
$anih.=inttodword(36);
$anih.=inttodword(count($Image));
$anih.=inttodword(count($Image));
$anih.=inttodword(0);
$anih.=inttodword(0);
$anih.=inttodword(0);
$anih.=inttodword(0);
$anih.=inttodword($Rate);  //jif rate
$anih.=inttodword(1); // flags

$ANI.="anih$anih";

/*$LIST.="INAM".inttodword(strlen($Title)).$Title;
$LIST.="IART".inttodword(strlen($Author)).$Author;*/

$ANI.=$LIST;

for($p=0;$p<count($Image);$p++)
 {
  $icon=aniImageIco($Image[$p]);
  $fram.="icon".inttodword(strlen($icon)).$icon;
 };

$LIST.="fram$fram";

$LIST="LIST".inttodword(strlen($LIST)).$LIST;
$ANI.=$LIST;


if(!$OneRate)
{
 $ANI.="rate";
 for($p=0;$p<count($Image);$p++)
  {
   $ANI.=inttodword($Rates[$p]);
  };
};

$ANI="RIFF".inttodword(strlen($ANI)).$ANI;


if($filename=="")
{
echo $ANI;
}
else
{
$f=fopen($filename,"w");
fwrite($f,$ANI);
fclose($f);
};




};






function aniImageIco($Images/*image or image array*/,$filename="")
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
$ret.=inttoword(2); //SOURCE
$ret.=inttoword($ImageCount); //ICONCOUNT


for($q=0;$q<$ImageCount;$q++)
{
$img=$Image[$q];

$Width=imagesx($img);
$Height=imagesy($img);


$C=imagecolorstotal($img);
if($C<=2)
{
 imagecolorallocate($img,255,0,0);
 imagecolorallocate($img,0,255,0);
};

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
$Size=40+($Width/(8/$BitCount)+(($Width/(8/$BitCount))%4))*$Height+(($Width/8+(($Width/8)%4)) * $Height);
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



 return $ret;


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



function to16(&$img)
{
 $col=imagecolorstotal($img);
 if($col<16)
  {
   for($p=0;$p<16-$col;$p++)
    imagecolorallocate($img,rand(0,255),rand(0,255),rand(0,255));
  };
};






?>