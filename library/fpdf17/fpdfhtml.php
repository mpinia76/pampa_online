<?php 
require('fpdf.php');
//require_once('lib/fpdf.inc.php');
require_once('color.inc.php');
require_once('htmlparser.inc.php');
//if (!defined('PARAGRAPH_STRING')) define('PARAGRAPH_STRING', '~~~');

function hex2dec($couleur = "#000000"){
    $R = substr($couleur, 1, 2);
    $rouge = hexdec($R);
    $V = substr($couleur, 3, 2);
    $vert = hexdec($V);
    $B = substr($couleur, 5, 2);
    $bleu = hexdec($B);
    $tbl_couleur = array();
    $tbl_couleur['R']=$rouge;
    $tbl_couleur['G']=$vert;
    $tbl_couleur['B']=$bleu;
    return $tbl_couleur;
}

//conversion pixel -> millimeter in 72 dpi
function px2mm($px){
    return $px*25.4/72;
}

function txtentities($html){
    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);
    return strtr($html, $trans);
}
define('FHR',0.58);
define('PDFTABLE_VERSION','1.9');
$PDF_ALIGN  = array('left'=>'L','center'=>'C','right'=>'R','justify'=>'J');
$PDF_VALIGN = array('top'=>'T','middle'=>'M','bottom'=>'B');
class fpdfhtmlHelper extends FPDF {
	var $B;
	var $I;
	var $U;
	var $HREF;
	var $fontList;
	var $issetfont;
	var $issetcolor;
	var $left;			//Toa do le trai cua trang
	var $right;			//Toa do le phai cua trang
	var $top;			//Toa do le tren cua trang
	var $bottom;		//Toa do le duoi cua trang
	var $width;			//Width of writable zone of page
	var $height;		//Height of writable zone of page
	var $defaultFontFamily ;
	var $defaultFontStyle;
	var $defaultFontSize;
	var $isNotYetSetFont;
	var $headerTable, $footerTable;
	var $paddingCell = 1;//(mm)
	var $paddingCell2 = 2;//2*$paddingCell
	var $spacingLine = 0;//(mm)
	var $spacingParagraph = 0;//(mm)
	
	function PDF($orientation='P', $unit='mm', $format='A4')
	{
	    //Call parent constructor
	    $this->FPDF($orientation, $unit, $format);
	    //Initialization
	    $this->B=0;
	    $this->I=0;
	    $this->U=0;
	    $this->HREF='';
	
	    $this->tableborder=0;
	    $this->tdbegin=false;
	    $this->tdwidth=0;
	    $this->tdheight=0;
	    $this->tdalign="L";
	    $this->tdbgcolor=false;
	
	    $this->oldx=0;
	    $this->oldy=0;
	
	    $this->fontlist=array("arial", "times", "courier", "helvetica", "symbol");
	    $this->issetfont=false;
	    $this->issetcolor=false;
	    $this->SetMargins(20,20,20);
		$this->SetAuthor('Pham Minh Dung');
		$this->_makePageSize();
		$this->isNotYetSetFont = true;
		$this->headerTable = $this->footerTable = '';
	}
	
	function SetPadding($s=1){
	$this->paddingCell = $s;
	$this->paddingCell2 = 2*$s;
}
function SetSpacing($linespacing=1,$paragraphspacing=2){
	$this->spacingLine = $linespacing;
	$this->spacingParagraph = $paragraphspacing;
}
function SetMargins($left,$top,$right=null,$bottom=null){
	parent::SetMargins($left, $top, $right);
	$this->bMargin = $bottom?$bottom:$top;
	$this->_makePageSize();
}

function SetLeftMargin($margin){
	parent::SetLeftMargin($margin);
	$this->_makePageSize();
}

function SetRightMargin($margin){
	parent::SetRightMargin($margin);
	$this->_makePageSize();
}

function SetHeaderFooter($header='',$footer=''){
	if ($header) $this->headerTable = $header;
	if ($footer) $this->footerTable = $footer;
}
function Header(){
	$this->_makePageSize();
	if ($this->headerTable){
		$this->x = $this->left;
		$this->y = 0;
		$this->htmltable($this->headerTable,0);
	}
}
function Footer(){
	if ($this->footerTable){
		$this->x = $this->left;
		$this->y = $this->bottom;
		$this->htmltable($this->footerTable,0);
	}
}
private function _makePageSize(){
	$this->left		= $this->lMargin;
	$this->right	= $this->w - $this->rMargin;
	$this->top		= $this->tMargin;
	$this->bottom	= $this->h - $this->bMargin;
	$this->width	= $this->right - $this->left;
	$this->height	= $this->bottom - $this->tMargin;
}
	/**
    * Allows you to control how the pdf is returned to the user, most of the time in CakePHP you probably want the string
    *
    * @param string $name name of the file.
    * @param string $destination where to send the document values: I, D, F, S
    * @return string if the $destination is S
    */
	function fpdfOutput ($name = 'page.pdf', $destination = 's') {
		// I: send the file inline to the browser. The plug-in is used if available.
		//    The name given by name is used when one selects the "Save as" option on the link generating the PDF.
		// D: send to the browser and force a file download with the name given by name.
		// F: save to a local file with the name given by name.
		// S: return the document as a string. name is ignored.
		return $this->Output($name, $destination);
	}

	function findAlign($html){
		if(ereg("justify", $html)){
			$this->ALIGN='J';
		}elseif(ereg("center", $html)){
			$this->ALIGN='C';
		}elseif(ereg("right", $html)){
			$this->ALIGN='R';
		}elseif(ereg("left", $html)){
			$this->ALIGN='L';
		}
	}

	function findUnderline($style){
		if(ereg("underline", $style)){
			$this->U=1;
		}
	}


	function trimespecialchar($html){

		//Reemplazo de caracteres especiales
		$html=str_replace("&aacute;",'á',$html);
		$html=str_replace("&eacute;",'é',$html);
		$html=str_replace("&iacute;",'í',$html);
		$html=str_replace("&oacute;",'ó',$html);
		$html=str_replace("&uacute;",'ú',$html);
		$html=str_replace("&Aacute;",'Á',$html);
		$html=str_replace("&Eacute;",'É',$html);
		$html=str_replace("&Iacute;",'Í',$html);
		$html=str_replace("&Oacute;",'Ó',$html);
		$html=str_replace("&Uacute;",'Ú',$html);
		$html=str_replace("&ordm;",'º',$html);
		$html=str_replace("&ntilde;",'ñ',$html);
		$html=str_replace("&Ntilde;",'Ñ',$html);
		$html=str_replace("&nbsp;",' ',$html);
		$html=str_replace("&lt;",'<',$html);
		$html=str_replace("&gt;",'>',$html);
		$html=str_replace("&amp;",'&',$html);
		$html=str_replace("&quot;",'"',$html);
		$html=str_replace("&ldquo;",'"',$html);
		$html=str_replace("&rdquo;",'"',$html);
		$html=str_replace("&Agrave;",'À',$html);
		$html=str_replace("&Egrave;",'È',$html);
		$html=str_replace("&Igrave;",'Ì',$html);
		$html=str_replace("&Ograve;",'Ò',$html);
		$html=str_replace("&Ugrave;",'Ù',$html);
		$html=str_replace("&agrave;",'à',$html);
		$html=str_replace("&egrave;",'è',$html);
		$html=str_replace("&igrave;",'ì',$html);
		$html=str_replace("&ograve;",'ò',$html);
		$html=str_replace("&ugrave; ",'ù',$html);
		$html=str_replace("&Auml;",'Ä',$html);
		$html=str_replace("&Euml;",'Ë',$html);
		$html=str_replace("&Iuml;",'Ï',$html);
		$html=str_replace("&Ouml;",'Ö',$html);
		$html=str_replace("&Uuml;",'Ü',$html);
		$html=str_replace("&Acirc;",'Â',$html);
		$html=str_replace("&Ecirc;",'Ê',$html);
		$html=str_replace("&Icirc;",'Î',$html);
		$html=str_replace("&Ocirc;",'Ô',$html);
		$html=str_replace("&Ucirc;",'Û',$html);
		$html=str_replace("&acirc;",'â',$html);
		$html=str_replace("&ecirc;",'ê',$html);
		$html=str_replace("&icirc;",'î',$html);
		$html=str_replace("&ocirc;",'ô',$html);
		$html=str_replace("&ucirc;",'û',$html);
		$html=str_replace("&auml;",'ä',$html);
		$html=str_replace("&euml;",'ë',$html);
		$html=str_replace("&iuml;",'ï',$html);
		$html=str_replace("&ouml;",'ö',$html);
		$html=str_replace("&uuml;",'ü',$html);
		$html=str_replace("&Atilde;",'Ã',$html);
		$html=str_replace("&Otilde;",'Õ',$html);
		$html=str_replace("&atilde;",'ã',$html);
		$html=str_replace("&otilde;",'õ',$html);
		$html=str_replace("&aring;",'å',$html);
		$html=str_replace("&Aring;",'Å',$html);
		$html=str_replace("&Ccedil;",'Ç',$html);
		$html=str_replace("&ccedil;",'ç',$html);
		$html=str_replace("&Yacute;",'Ý',$html);
		$html=str_replace("&yacute;",'ý',$html);
		$html=str_replace("&yuml;",'ÿ',$html);
		$html=str_replace("&iquest;",'¿',$html);
		$html=str_replace("&ndash;",'-',$html);
		$html=str_replace("&iexcl;",'¡',$html);
		$html=str_replace("&middot;",'·',$html);
		$html=str_replace("&hellip;",'...',$html);
		$html=str_replace("&rsquo;","'",$html);
		$html=str_replace("&acute;","'",$html);
		$html=str_replace("&lsquo;","'",$html);
		$html=str_replace("&#39;","'",$html);
		$html=str_replace("&mu;","µ",$html);
		$html=str_replace("&micro;","µ",$html);
		$html=str_replace("&deg;","°",$html);
		$html=str_replace("&prime;","'",$html); 
		$html=str_replace("&times;","×",$html); 
		$html=str_replace("&oslash;","ø",$html); 
		$html=str_replace("&reg;","®",$html); 
		$html=str_replace("&beta;","ß",$html); 
		$html=str_replace("&alpha;","a",$html); 
		$html=str_replace("&laquo;","«",$html); 
		$html=str_replace("&mdash;","-",$html);
		$html=str_replace("&rarr;","->",$html);
		$html=str_replace("&raquo;","»",$html); 
		$html=str_replace("&ugrave;","ù",$html); 
		
		return $html;
	}
	
	function WriteHTML2($html)
{
    //HTML parser
    $html=str_replace("\n", ' ', $html);
    $a=preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            //Text
            if($this->HREF)
                $this->PutLink($this->HREF, $e);
            else
                $this->Write(5, $e);
        }
        else
        {
            //Tag
            if($e{0}=='/')
                $this->CloseTag(strtoupper(substr($e, 1)));
            else
            {
                //Extract attributes
               /* $e =str_replace('style="','', $e);
                $e =str_replace(': ','=', $e);
                $e =str_replace(';"','', $e);*/
            	$attr=array();
                $heightpos = stripos($e,'height:');
                if($heightpos){
                	$heightSTR=substr($e,$heightpos+8);
                	$heightfin = strpos($heightSTR,';');
                	$cant=strlen($heightSTR)-$heightfin;
                	$attr['HEIGHT']=substr($heightSTR,0,-$cant);
                	
                }
               
                
                $widthpos = stripos($e,'width:');
                if($widthpos){
                	$widthSTR=substr($e,$widthpos+7);
                	$widthfin = strpos($widthSTR,';');
                	$cant=strlen($widthSTR)-$widthfin;
                	$attr['WIDTH']=substr($widthSTR,0,-$cant);
                }
               
                
                
              //   $e =str_replace('background-color','bgcolor', $e);
                $a2=explode(' ', $e);
                $tag=strtoupper(array_shift($a2));
              
                foreach($a2 as $v)
                    if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$', $v, $a3))
                        $attr[strtoupper($a3[1])]=$a3[2];
                 
                $this->OpenTag($tag, $attr);
            }
        }
    }
}

	function WriteHTML($html)
{
    $html=strip_tags($html, "<b><u><ul><li><i><a><img><p><strong><em><tr><blockquote><hr><td><tr><table><sup>"); //remove all unsupported tags
    $html=str_replace("\n", '', $html); //replace carriage returns by spaces
    $html=str_replace("\t", '', $html); //replace carriage returns by spaces
    
    /*$rgbpos = strpos($html,'rgb(');
    if($rgbpos){
    	$rgbfin = strpos($html,')');
    	$rgb = substr($html,$rgbpos,$rgbfin);
    	$html =str_replace($rgb,trim($rgb), $html);
    }*/
    
     $html =str_replace('background-color','bgcolor', $html);
    $html = $this->trimespecialchar($html);
    $start = strpos(strtolower($html), '<table');
    $end = strpos(strtolower($html), '</table');
    if($start!==false && $end!==false)
    {
        $this->WriteHTML2(substr($html, 0, $start).'<BR>');
		
        $tableVar = substr($html, $start, $end-$start).'</table>';
       
        $tableVar =str_replace('px', '', $tableVar);
        $styleini = strpos($tableVar,'style=');
        while($styleini){
        	$primero = strpos($tableVar,'"',$styleini+1);
	    	$stylefin = strpos($tableVar,'"',$primero+1);
	    	$style = substr($tableVar,$styleini,$stylefin-$styleini+2);
	    	$attrs = str_replace('style="','', $style);
	    	$attrs = str_replace(';"','', $attrs);
	    	$attrs = str_replace(': ','=', $attrs);
	    	$attrs = trim($attrs);
	    	$attrs = str_replace(';',' ', $attrs);
	    	$attrs = str_replace('width','name', $attrs);
	    	$rgbpos = strpos($attrs,'rgb(');
		    if($rgbpos){
		    	$rgbfin = strpos($attrs,')');
		    	$rgb = substr($attrs,$rgbpos,$rgbfin-$rgbpos);
		    	$rgbarray = explode(',',$rgb);
		    	$rgbtrim = trim($rgbarray[0]).','.trim($rgbarray[1]).','.trim($rgbarray[2]);
		    	$attrs =str_replace($rgb,$rgbtrim, $attrs);
		    }
	    	$tableVar =str_replace($style, $attrs, $tableVar);
	    	//$html =str_replace($rgb,trim($rgb), $html);
	    	
	    	$styleini = strpos($tableVar,'style=');
	    }
        $tableVar =str_replace('"', '', $tableVar);
        
        $this->htmltable($tableVar);
         $htmlR = substr($html, $end+8, strlen($html));
        $this->WriteHTML($htmlR);
        /*$tableData = $this->ParseTable($tableVar);
        for($i=1;$i<=count($tableData[0]);$i++)
        {
            if($this->CurOrientation=='L')
                $w[] = abs(120/(count($tableData[0])-1))+24;
            else
                $w[] = abs(120/(count($tableData[0])-1))+5;
        }
        $this->WriteTable($tableData, $w);

        $this->WriteHTML2(substr($html, $end+8, strlen($html)-1).'<BR>');*/
    }
    else
    {
        $this->WriteHTML2($html);
    }

}

function OpenTag($tag, $attr)
{
    //Opening tag
    switch($tag){

        case 'SUP':
           // if($attr['SUP'] != '') {    
                //Set current font to: Bold, 6pt     
                $this->SetFont('', '', 6);
                //Start 125cm plus width of cell to the right of left margin         
                //Superscript "1"
                //$this->Cell(2, 2, $attr['SUP'], 0, 0, 'L');
            //}
            break;
		case 'LI':
                $this->Ln(5);
                //$this->SetTextColor(190, 0, 0);
                $this->Write(5, '     » ');
               // $this->mySetTextColor(-1);
                break;
        case 'TABLE': // TABLE-BEGIN
            $this->Ln(10);
        	if( $attr['BORDER'] != '' ) $this->tableborder=$attr['BORDER'];
            else $this->tableborder=0;
            break;
        case 'TR': //TR-BEGIN
            break;
        case 'TD': // TD-BEGIN
            if( $attr['WIDTH'] != '' ) $this->tdwidth=($attr['WIDTH']/4);
            else $this->tdwidth=20; // SET to your own width if you need bigger fixed cells
            if( $attr['HEIGHT'] != '') $this->tdheight=($attr['HEIGHT']/6);
            else $this->tdheight=6; // SET to your own height if you need bigger fixed cells
            if( $attr['ALIGN'] != '' ) {
                $align=$attr['ALIGN'];        
                if($align=="LEFT") $this->tdalign="L";
                if($align=="CENTER") $this->tdalign="C";
                if($align=="RIGHT") $this->tdalign="R";
            }
            else $this->tdalign="L"; // SET to your own
            if( $attr['BGCOLOR'] != '' ) {
                if (strchr($attr['BGCOLOR'],'rgb')){
                	$attr['BGCOLOR'] = str_replace('rgb(','',$attr['BGCOLOR']);
                	$attr['BGCOLOR'] = str_replace(')','',$attr['BGCOLOR']);
                	$coul = explode(',',$attr['BGCOLOR']);
                	$this->SetFillColor($coul[0], $coul[1], $coul[2]);
                }
                else{
            		$coul=hex2dec($attr['BGCOLOR']);
            		$this->SetFillColor($coul['R'], $coul['G'], $coul['B']);
                }
                    
                    $this->tdbgcolor=true;
                }
            $this->tdbegin=true;
            break;

        case 'HR':
            if( $attr['WIDTH'] != '' )
                $Width = $attr['WIDTH'];
            else
                $Width = $this->w - $this->lMargin-$this->rMargin;
            $x = $this->GetX();
            $y = $this->GetY();
            $this->SetLineWidth(0.2);
            $this->Line($x, $y, $x+$Width, $y);
            $this->SetLineWidth(0.2);
            $this->Ln(1);
            break;
        case 'STRONG':
            $this->SetStyle('B', true);
            break;
        case 'EM':
            $this->SetStyle('I', true);
            break;
        case 'B':
        case 'I':
        case 'U':
            $this->SetStyle($tag, true);
            break;
        case 'A':
            $this->HREF=$attr['HREF'];
            break;
        case 'IMG':
            if(isset($attr['SRC']) and (isset($attr['WIDTH']) or isset($attr['HEIGHT']))) {
                if(!isset($attr['WIDTH']))
                    $attr['WIDTH'] = 0;
                if(!isset($attr['HEIGHT']))
                    $attr['HEIGHT'] = 0;
                $attr['SRC']=str_replace(WEB_PATH,APP_PATH,$attr['SRC']);    
                if (is_file($attr['SRC'])) {
                	 $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
                	 $this->Ln(px2mm($attr['HEIGHT']));
                }
                   
            }
            break;
        //case 'TR':
        case 'BLOCKQUOTE':
        case 'BR':
            $this->Ln(5);
            break;
        case 'P':
            $this->Ln(5);
            break;
        case 'FONT':
            if (isset($attr['COLOR']) and $attr['COLOR']!='') {
                $coul=hex2dec($attr['COLOR']);
                $this->SetTextColor($coul['R'], $coul['G'], $coul['B']);
                $this->issetcolor=true;
            }
            if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist)) {
                $this->SetFont(strtolower($attr['FACE']));
                $this->issetfont=true;
            }
            if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist) and isset($attr['SIZE']) and $attr['SIZE']!='') {
                $this->SetFont(strtolower($attr['FACE']), '', $attr['SIZE']);
                $this->issetfont=true;
            }
            break;
    }
}

function CloseTag($tag)
{
    //Closing tag
    if($tag=='SUP') {
		$this->SetFont ( 'times', '', 12 );
    }

    if($tag=='TD') { // TD-END
        $this->tdbegin=false;
        $this->tdwidth=0;
        $this->tdheight=0;
        $this->tdalign="L";
        $this->tdbgcolor=false;
    }
    if($tag=='TR') { // TR-END
        $this->Ln();
    }
    if($tag=='TABLE') { // TABLE-END
        //$this->Ln();
        $this->tableborder=0;
    }

    if($tag=='STRONG')
        $tag='B';
    if($tag=='EM')
        $tag='I';
    if($tag=='B' or $tag=='I' or $tag=='U')
        $this->SetStyle($tag, false);
    if($tag=='A')
        $this->HREF='';
    if($tag=='FONT'){
        if ($this->issetcolor==true) {
            $this->SetTextColor(0);
        }
        if ($this->issetfont) {
            $this->SetFont('arial');
            $this->issetfont=false;
        }
    }
}

function SetStyle($tag, $enable)
{
    //Modify style and select corresponding font
    $this->$tag+=($enable ? 1 : -1);
    $style='';
    foreach(array('B', 'I', 'U') as $s)
        if($this->$s>0)
            $style.=$s;
    $this->SetFont('', $style);
}

function PutLink($URL, $txt)
{
    //Put a hyperlink
    $this->SetTextColor(0, 0, 255);
    $this->SetStyle('U', true);
    $this->Write(5, $txt, $URL);
    $this->SetStyle('U', false);
    $this->SetTextColor(0);
}
	
	var $widths;
	var $aligns;
	
	function SetWidths($w)
	{
	    //Set the array of column widths
	    $this->widths=$w;
	}
	
	function SetAligns($a)
	{
	    //Set the array of column alignments
	    $this->aligns=$a;
	}
	
	function Row($data)
	{
	    //Calculate the height of the row
	    $nb=0;
	    for($i=0;$i<count($data);$i++)
	        $nb=max($nb, $this->NbLines($this->widths[$i], $data[$i]));
	    $h=5*$nb;
	    //Issue a page break first if needed
	    $this->CheckPageBreak($h);
	    //Draw the cells of the row
	    for($i=0;$i<count($data);$i++)
	    {
	        $w=$this->widths[$i];
	        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
	        //Save the current position
	        $x=$this->GetX();
	        $y=$this->GetY();
	        //Draw the border
	        $this->Rect($x, $y, $w, $h);
	        //Print the text
	        $this->MultiCell($w, 5, $data[$i], 0, $a);
	        //Put the position to the right of the cell
	        $this->SetXY($x+$w, $y);
	    }
	    //Go to the next line
	    $this->Ln($h);
	}
	
	function CheckPageBreak($h)
	{
	    //If the height h would cause an overflow, add a new page immediately
	    if($this->GetY()+$h>$this->PageBreakTrigger)
	        $this->AddPage($this->CurOrientation);
	}
	
	function NbLines($w, $txt)
	{
	    //Computes the number of lines a MultiCell of width w will take
	    $cw=&$this->CurrentFont['cw'];
	    if($w==0)
	        $w=$this->w-$this->rMargin-$this->x;
	    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	    $s=str_replace("\r", '', $txt);
	    $nb=strlen($s);
	    if($nb>0 and $s[$nb-1]=="\n")
	        $nb--;
	    $sep=-1;
	    $i=0;
	    $j=0;
	    $l=0;
	    $nl=1;
	    while($i<$nb)
	    {
	        $c=$s[$i];
	        if($c=="\n")
	        {
	            $i++;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	            continue;
	        }
	        if($c==' ')
	            $sep=$i;
	        $l+=$cw[$c];
	        if($l>$wmax)
	        {
	            if($sep==-1)
	            {
	                if($i==$j)
	                    $i++;
	            }
	            else
	                $i=$sep+1;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	        }
	        else
	            $i++;
	    }
	    return $nl;
	}
	


/**
 * @return int
 * Tra ve chieu cao cua 1 dong theo font hien hanh
 */
function getLineHeight($fontSize = 0){
	if ($fontSize == 0) $fontSize = $this->FontSizePt;
	return $fontSize*2/$this->k;
}

private function _cellHeight(&$c){
	$maxw = $c['w0']-$this->paddingCell;
	$h = 0;
	$x = $this->paddingCell;
	$countline = 0;
	$maxline = 0;
	$c['hline'] = array();
	$c['wlinet'] = array(array(0,0));
	$c['wlines'] = array(0);
	$space = 0;
	foreach ($c['font'] as &$f){
		$this->_setFontText($f);
		$hl = $this->getLineHeight();
		if ($maxline<$hl || $x==$this->paddingCell) $maxline=$hl;
		if (!isset($f['space'])) continue;
		$space = $f['space'];
		foreach ($f['line'] as $i=>&$l){
			if ($x!=$this->paddingCell) $x+=$space;
			if (isset($l['str'])&&is_array($l['str']))
			foreach ($l['str'] as &$t){
				if (!is_array($t)) continue;
				if ($x==$this->paddingCell||$x+$t[1]<$maxw){
					$c['wlinet'][$countline][0] += $t[1];
					$c['wlinet'][$countline][1]++;
					$x += $t[1]+(($x>$this->paddingCell)?$space:0);
				}else{//auto break line
					$h+=$maxline*FHR + $this->spacingLine;
					$c['hline'][] = $maxline*FHR + $this->spacingLine;
					$c['wlines'][$countline] = $x-$this->paddingCell;
					$c['autobr'][$countline] = 1;
					$maxline=$hl;$countline++;$x = $t[1]+$space;
					$c['wlinet'][$countline] = array($t[1],1);
				}
				$t[2] = $countline;
			}
			if ($l=='br'){
				$h+=$maxline*FHR + max($this->spacingLine,$this->spacingParagraph);
				$c['hline'][] = $maxline*FHR + $this->spacingLine;
				$c['wlines'][$countline] = $x-$this->paddingCell;
				$maxline=$hl;$countline++;$x = $this->paddingCell;
				$c['wlinet'][$countline] = array(0,0);
			}
		}
	}
	$c['wlines'][$countline] = $x-$space-$this->paddingCell;
	if ($maxline){
		$h+=$maxline;
		$c['hline'][] = $maxline*FHR;
	}
	$c['maxh'] = $h;
	return $h;
}
private function _drawCellAligned($x0,$y0,&$c){
	$maxh = $c['h0'];
	$maxw = $c['w0']-$this->paddingCell2;
	$curh = $c['maxh'];
	$x=$y=0;//Top by default
	if ($c['va']=='M') $y = ($maxh-$curh)/2;//Middle
	elseif ($c['va']=='B') $y = $maxh-$curh;//Bottom
	$curline = 0;
	$morespace = 0;
	$cl = $c['hline'][$curline];
	$this->_cellHorzAlignLine($c,$curline,$maxw,$x,$morespace);
	foreach ($c['font'] as &$f){
		$this->_setFontText($f);
		if (isset($f['color'])){
			$color = Color::HEX2RGB($f['color']);
			$this->SetTextColor($color[0],$color[1],$color[2]);
		}else unset($color);
		$hl = $this->getLineHeight();
		if (!isset($f['space'])) continue;
		$space = $f['space'];
		foreach ($f['line'] as $i=>&$l){
			if (isset($l['str'])&&is_array($l['str']))
			foreach ($l['str'] as &$t){
				if ($t[2]!=$curline){
					$y += $cl;$curline++;$cl = $c['hline'][$curline];
					$this->_cellHorzAlignLine($c,$curline,$maxw,$x,$morespace);
				}
				$this->x = $x+$x0;
				$this->y = $y+$y0+$cl;
				$this->Cell($t[1],0,$t[0]);
				$x += $t[1]+$space+$morespace;
			}
			if ($l=='br'){
				$y += $cl;$curline++;$cl = $c['hline'][$curline];
				$this->_cellHorzAlignLine($c,$curline,$maxw,$x,$morespace);
			}
		}
		if (isset($color))
			$this->SetTextColor(0);
	}
}
private function _cellHorzAlignLine(&$c,$line,$maxw,&$x,&$morespace){
	$morespace = 0;
	$x = $this->paddingCell;//Left by default
	if (!isset($c['wlines'][$line])) return ;
	if ($c['a']=='C'){//Center
		$x = ($maxw-$c['wlines'][$line])/2;
	}elseif ($c['a']=='R'){
		$x = $maxw-$c['wlines'][$line];
	}elseif ($c['a']=='J' && $c['wlinet'][$line][1]>1
		&& isset($c['autobr'][$line])){//Justify
		$morespace = ($maxw-$c['wlines'][$line])/($c['wlinet'][$line][1]-1);
	}
}

private function _calWidth($w)
{
	$p = strpos($w,'%');
	if ($p!==false){
		return intval(substr($w,0,$p)*$this->width/100);
	}else return intval($w);
}	

private function _tableParser(&$html){
	$t = new TreeHTML(new HTMLParser($html), 0);
	$row	= $col	= -1;
	$table['nc'] = $table['nr'] = 0;
	$table['repeat'] = array();
	$cell   = array();
	$fontopen = false;
	$tdopen = false;
	foreach ($t->name as $i=>$element){
		if ($fontopen && $t->type[$i]==NODE_TYPE_ENDELEMENT
			&& (in_array($element,array('table','tr','td','font'))))
				$fontopen = false;
		if ($tdopen && $t->type[$i]==NODE_TYPE_ENDELEMENT
			&& (in_array($element,array('table','tr','td')))
			&& !isset($cell[$row][$col]['miw'])){
				$c = &$cell[$row][$col];
				$c['miw'] = $c['maw'] = 0;
				$tdopen = false;
		}
		if ($t->type[$i] != NODE_TYPE_ELEMENT && $t->type[$i] != NODE_TYPE_TEXT) continue;
		switch ($element){
			case 'table':
				$tdopen = 0;
				$a	= &$t->attribute[$i];
				if (isset($a['width']))		$table['w']	= $this->_calWidth($a['width']);
				if (isset($a['height']))	$table['h']	= intval($a['height']);
				if (isset($a['align']))		$table['a']	= $align[strtolower($a['align'])];
				$table['border'] = (isset($a['border']))?	$a['border']: 0;
				if (isset($a['bgcolor']))	$table['bgcolor'][-1]	= $a['bgcolor'];
				$table['nobreak'] = isset($a['nobreak']);
				break;
			case 'tr':
				$tdopen = 0;
				$row++;
				$table['nr']++;
				$col = -1;
				$a	= &$t->attribute[$i];
				if (isset($a['bgcolor']))	$table['bgcolor'][$row]	= $a['bgcolor'];
				if (isset($a['repeat']))	$table['repeat'][]		= $row;
				break;
			case 'td':
				$tdopen = 1;
				$col++;while (isset($cell[$row][$col])) $col++;
				//Update number column
				if ($table['nc'] < $col+1)		$table['nc']		= $col+1;
				$cell[$row][$col] = array();
				$c = &$cell[$row][$col];
				$a	= &$t->attribute[$i];
				if (isset($a['width']))		$c['w']		= intval($a['width']);
				if (isset($a['height']))	$c['h']		= intval($a['height']);
				$c['a'] = isset($a['align'])?$this->getAlign($a['align']):'L';
				$c['va']= isset($a['valign'])?$this->getVAlign($a['valign']):'T';
				if (isset($a['border']))	$c['border']	= $a['border'];
					else					$c['border']	= $table['border'];
				if (isset($a['bgcolor']))	$c['bgcolor']	= $a['bgcolor'];
				$cs = $rs = 1;
				if (isset($a['colspan']) && $a['colspan']>1)	$cs = $c['colspan']	= intval($a['colspan']);
				if (isset($a['rowspan']) && $a['rowspan']>1)	$rs = $c['rowspan']	= intval($a['rowspan']);
				if (isset($a['size']))		$c['font'][0]['size']   	= $a['size'];
				if (isset($a['family']))	$c['font'][0]['family'] 	= $a['family'];
				if (isset($a['style'])){
					$STYLE     = explode(",", strtoupper($a['style']));
					$fontStyle = '';
					foreach($STYLE AS $style)  $fontStyle .= substr(trim($style), 0, 1);
					$c['font'][0]['style'] = $fontStyle;
				}
				if (isset($a['color']))		$c['font'][0]['color'] 		= $a['color'];
				//Chiem dung vi tri de danh cho cell span
				for ($k=$row;$k<$row+$rs;$k++) for($l=$col;$l<$col+$cs;$l++){
					if ($k-$row || $l-$col)
						$cell[$k][$l] = 0;
				}
				if (isset($a['nowrap']))	$c['nowrap']= 1;
				$fontopen = true;
				if (!isset($c['font'])) $c['font'][] = array();
				break;
			case 'Text':
				$c = &$cell[$row][$col];
				if (!$fontopen || !isset($c['font'])) $c['font'][] = array();
				$f = &$c['font'][count($c['font'])-1];
				$this->_setTextAndSize($c,$f,$this->_html2text($t->value[$i]));
				break;
			case 'font':
				$a	= &$t->attribute[$i];
				$c = &$cell[$row][$col];
				$c['font'][] = array();
				$f = &$c['font'][count($c['font'])-1];
				if (isset($a['size']))		$f['size']   	= $a['size'];
				if (isset($a['family']))	$f['family'] 	= $a['family'];
				if (isset($a['style'])){
					$STYLE     = explode(",", strtoupper($a['style']));
					$fontStyle = '';
					foreach($STYLE AS $style)  $fontStyle .= substr(trim($style), 0, 1);
					$f['style'] = $fontStyle;
				}
				if (isset($a['color']))		$f['color'] 		= $a['color'];
				break;
			case 'img':
				$a	= &$t->attribute[$i];
				if (isset($a['src'])){
					$this->_setImage($c,$a);
				}
				break;
			case 'br':
				$c = &$cell[$row][$col];
				$cn = isset($c['font'])?count($c['font'])-1:0;
				$c['font'][$cn]['line'][] = 'br';
				break;
		}
	}
	$table['cells'] = $cell;
	$table['wc']	= array_pad(array(),$table['nc'],array('miw'=>0,'maw'=>0));
	$table['hr']	= array_pad(array(),$table['nr'],0);
	return $table;
}

private function _setTextAndSize(&$cell,&$f, $text){
	if ($text=='') return;
	$this->_setFontText($f);
	if (!isset($f['line'][0])){
		$f['line'][0]['min'] = $f['line'][0]['max'] = 0;
	}
	$text = preg_split('/[\s]+/',$text,-1, PREG_SPLIT_NO_EMPTY);
	$l = &$f['line'][count($f['line'])-1];
	if ($l=='br'){
		$f['line'][] = array('min'=>0,'max'=>0,'str'=>array());
		$l = &$f['line'][count($f['line'])-1];
	}
	if (!isset($f['space'])) $f['space'] = $this->GetStringWidth(' ');
	$ct = count($text);
	foreach ($text as $item){
		$s = $this->GetStringWidth($item);
		if ($l['min']<$s) $l['min']=$s;
		$l['max'] += $s;
		if ($ct>1) $l['max'] += $f['space'];
		$l['str'][] =  array($item,$s);
	}
	if (isset($cell['nowrap'])) $l['min'] = $l['max'];
	if (!isset($cell['miw']) || $cell['miw']-$this->paddingCell2<$l['min'])
		$cell['miw']=$l['min']+$this->paddingCell2;
	if (!isset($cell['maw']) || $cell['maw']-$this->paddingCell2<$l['max'])
		$cell['maw']=$l['max']+$this->paddingCell2;
}

private function _setImage(&$c, &$a){
	$path = $a['src'];
	if (!is_file($path)){
		$this->Error('Image is not exists: '.$path);
	}else{
		list($u,$d) = $this->_getResolution($path);
		$c['img'] = $path;
		list($c['w'],$c['h'],) = getimagesize($path);
		if (isset($a['width'])) $c['w'] = $a['width'];
		if (isset($a['height'])) $c['h'] = $a['height'];
		$scale = 1;
		if ($u==1) $scale = 25.4/$d;
		elseif ($u==2) $scale = 10/$d;
		$c['w'] = intval($c['w']*$scale);
		$c['h'] = intval($c['h']*$scale);
	}
}
private function _getResolution($path)
{
	$pos=strrpos($path,'.');
	if(!$pos)
		$this->Error('Image file has no extension and no type was specified: '.$path);
	$type=substr($path,$pos+1);
	$type=strtolower($type);
	if($type=='jpeg')
		$type='jpg';
	if ($type!='jpg')
		$this->Error('Unsupported image type: '.$path);
	$f = fopen($path,'r');
	fseek($f,13,SEEK_SET);
	$info = fread($f,3);
    fclose($f);
    $iUnit = ord($info{0});
    $iX = ord($info{1}) * 256 + ord($info{2});
    return array($iUnit, $iX);
}
private function _html2text($text){
	$text = str_replace('&nbsp;',' ',$text);
	$text = str_replace('&lt;','<',$text);
	return $text;
}

/**
 * table	Array of (w, h, bc, nr, wc, hr, cells)
 * w		Width of table
 * h		Height of table
 * bc		Number column
 * nr		Number row
 * hr		List of height of each row
 * wc		List of width of each column
 * cells	List of cells of each rows, cells[i][j] is a cell in table
 */
private function _tableColumnWidth(&$table){
	$cs = &$table['cells'];
	$nc = $table['nc'];
	$nr = $table['nr'];
	$listspan = array();
	//Xac dinh do rong cua cac cell va cac cot tuong ung
	for ($j=0;$j<$nc;$j++){
		$wc = &$table['wc'][$j];
		for ($i=0;$i<$nr;$i++){
			if (isset($cs[$i][$j]['miw'])){
				$c   = &$cs[$i][$j];
				if (isset($c['nowrap'])) $c['miw'] = $c['maw'];
				if (isset($c['w'])){
					if ($c['miw']<$c['w']) $c['miw'] = $c['w'];
					elseif ($c['miw']>$c['w']) $c['w'] = $c['miw']+$this->paddingCell2;
					if (!isset($wc['w'])) $wc['w'] = 1;
				}
				if ($c['maw']<$c['miw']) $c['maw'] = $c['miw'];
				if (!isset($c['colspan'])){
					if ($wc['miw'] < $c['miw']) $wc['miw']=$c['miw'];
					if ($wc['maw'] < $c['maw']) $wc['maw']=$c['maw'];
					if (isset($wc['w']) && $wc['w'] < $wc['miw'])
						$wc['w']=$wc['miw'];
				}else $listspan[] = array($i,$j);
			}
		}
	}
	//Xac dinh su anh huong cua cac cell colspan len cac cot va nguoc lai
	$wc = &$table['wc'];
	foreach ($listspan as $span){
		list($i,$j) = $span;
		$c = &$cs[$i][$j];
		$lc = $j + $c['colspan'];
		if ($lc > $nc) $lc = $nc;

		$wis = $wisa = 0;
		$was = $wasa = 0;
		$list = array();
		for($k=$j;$k<$lc;$k++){
			$wis += $wc[$k]['miw'];
			$was += $wc[$k]['maw'];
			if (!isset($c['w'])){
				$list[] = $k;
				$wisa += $wc[$k]['miw'];
				$wasa += $wc[$k]['maw'];
			}
		}
		if ($c['miw'] > $wis){
			if (!$wis){//Cac cot chua co kich thuoc => chia deu
				for($k=$j;$k<$lc;$k++) $wc[$k]['miw'] = $c['miw']/$c['colspan'];
			}elseif (!count($list)){//Khong co cot nao co kich thuoc auto => chia deu phan du cho tat ca
				$wi = $c['miw'] - $wis;
				for($k=$j;$k<$lc;$k++)
					$wc[$k]['miw'] += ($wc[$k]['miw']/$wis)*$wi;
			}else{//Co mot so cot co kich thuoc auto => chia deu phan du cho cac cot auto
				$wi = $c['miw'] - $wis;
				foreach ($list as $_z2=>$k)
					$wc[$k]['miw'] += ($wc[$k]['miw']/$wisa)*$wi;
			}
		}
		if ($c['maw'] > $was){
			if (!$wis){//Cac cot chua co kich thuoc => chia deu
				for($k=$j;$k<$lc;$k++) $wc[$k]['maw'] = $c['maw']/$c['colspan'];
			}elseif (!count($list)){//Khong co cot nao co kich thuoc auto => chia deu phan du cho tat ca
				$wi = $c['maw'] - $was;
				for($k=$j;$k<$lc;$k++)
					$wc[$k]['maw'] += ($wc[$k]['maw']/$was)*$wi;
			}else{//Co mot so cot co kich thuoc auto => chia deu phan du cho cac cot auto
				$wi = $c['maw'] - $was;
				foreach ($list as $_z2=>$k)
					$wc[$k]['maw'] += ($wc[$k]['maw']/$wasa)*$wi;
			}
		}
	}
}

/**
 * @desc Xac dinh chieu rong cua table
 */
private function _tableWidth(&$table){
	$wc = &$table['wc'];
	$nc = $table['nc'];
	$a = 0;
	for ($i=0;$i<$nc;$i++){
		$a += isset($wc[$i]['w']) ? $wc[$i]['miw'] : $wc[$i]['maw'];
	}
	if ($a > $this->width) $table['w'] = $this->width;

	if (isset($table['w'])){
		$wis = $wisa = 0;
		$list = array();
		for ($i=0;$i<$nc;$i++){
			$wis += $wc[$i]['miw'];
			if (!isset($wc[$i]['w'])){ $list[] = $i;$wisa += $wc[$i]['miw'];}
		}
		if ($table['w'] > $wis){
			if (!count($list)){
				//Khong co cot nao co kich thuoc auto => chia deu phan du cho tat ca
				$wi = ($table['w'] - $wis)/$nc;
				for($k=0;$k<$nc;$k++)
					$wc[$k]['miw'] += $wi;
			}else{
				//Co mot so cot co kich thuoc auto => chia deu phan du cho cac cot auto
				$wi = ($table['w'] - $wis)/count($list);
				foreach ($list as $k)
					$wc[$k]['miw'] += $wi;
			}
		}
		for ($i=0;$i<$nc;$i++){
			$a = $wc[$i]['miw'];
			unset($wc[$i]);
			$wc[$i] = $a;
		}
	}else{
		$table['w'] = $a;
		for ($i=0;$i<$nc;$i++){
			$a = isset($wc[$i]['w']) ? $wc[$i]['miw'] : $wc[$i]['maw'];
			unset($wc[$i]);
			$wc[$i] = $a;
		}
	}
	$table['w'] = array_sum($wc);
}

private function _tableHeight(&$table){
	$cs = &$table['cells'];
	$nc = $table['nc'];
	$nr = $table['nr'];
	$listspan = array();
	for ($i=0;$i<$nr;$i++){
		$hr = &$table['hr'][$i];
		for ($j=0;$j<$nc;$j++){
			if (isset($cs[$i][$j]['miw'])){
				$c = &$cs[$i][$j];
				$this->_tableGetWCell($table, $i,$j);//create $c['x0'], $c['w0']

				$ch = $this->_cellHeight($c);
				$c['ch'] = $ch;

				if (isset($c['h']) && $c['h'] > $ch) $ch = $c['h'];

				if (isset($c['rowspan'])) $listspan[] = array($i,$j);
				elseif ($hr < $ch) $hr = $ch;
				$c['mih'] = $ch;
			}
		}
	}
	$hr = &$table['hr'];
	foreach ($listspan as $span){
		list($i,$j) = $span;
		$c = &$cs[$i][$j];
		$lr = $i + $c['rowspan'];
		if ($lr > $nr) $lr = $nr;
		$hs = $hsa = 0;
		$list = array();
		for($k=$i;$k<$lr;$k++){
			$hs += $hr[$k];
			if (!isset($c['h'])){
				$list[] = $k;
				$hsa += $hr[$k];
			}
		}
		if ($c['mih'] > $hs){
			if (!$hs){//Cac dong chua co kich thuoc => chia deu
				for($k=$i;$k<$lr;$k++) $hr[$k] = $c['mih']/$c['rowspan'];
			}elseif (!count($list)){
				//Khong co dong nao co kich thuoc auto => chia deu phan du cho tat ca
				$hi = $c['mih'] - $hs;
				for($k=$i;$k<$lr;$k++)
					$hr[$k] += ($hr[$k]/$hs)*$hi;
			}else{
				//Co mot so dong co kich thuoc auto => chia deu phan du cho cac dong auto
				$hi = $c['mih'] - $hsa;
				foreach ($list as $k)
					$hr[$k] += ($hr[$k]/$hsa)*$hi;
			}
		}
	}
	$table['repeatH'] = 0;
	if (count($table['repeat'])){
		foreach ($table['repeat'] as $i) $table['repeatH'] += $hr[$i];
	}else $table['repeat'] = 0;
	$tth = 0;
	foreach ($hr as $v) $tth+=$v;
	$table['tth'] = $tth;
}

/**
 * @desc Xac dinh toa do va do rong cua mot cell
 */
private function _tableGetWCell(&$table, $i,$j){
	$c = &$table['cells'][$i][$j];
	if ($c){
		if (isset($c['x0'])) return array($c['x0'], $c['w0']);
		$x = 0;
		$wc = &$table['wc'];
		for ($k=0;$k<$j;$k++) $x += $wc[$k];
		$w = $wc[$j];
		if (isset($c['colspan'])){
			for ($k=$j+$c['colspan']-1;$k>$j;$k--)
				$w += $wc[$k];
		}
		$c['x0'] = $x;
		$c['w0'] = $w;
		return array($x, $w);
	}
	return array(0,0);
}

private function _tableGetHCell(&$table, $i,$j){
	$c = &$table['cells'][$i][$j];
	if ($c){
		if (isset($c['h0'])) return $c['h0'];
		$hr = &$table['hr'];
		$h = $hr[$i];
		if (isset($c['rowspan'])){
			for ($k=$i+$c['rowspan']-1;$k>$i;$k--)
				$h += $hr[$k];
		}
		$c['h0'] = $h;
		return $h;
	}
	return 0;
}

private function _tableRect($x, $y, $w, $h, $type=1){
/*
	if (strlen($type)==4)
	{
		$x2 = $x + $w; $y2 = $y + $h;
		if (intval($type{0})) $this->Line($x , $y , $x2, $y );
		if (intval($type{1})) $this->Line($x2, $y , $x2, $y2);
		if (intval($type{2})) $this->Line($x , $y2, $x2, $y2);
		if (intval($type{3})) $this->Line($x , $y , $x , $y2);
*/
	$type4 = explode(",",$type);
	if (sizeof($type4) > 1) {
		$width = $this->LineWidth;
		$x2 = $x + $w; $y2 = $y + $h;
		if (floatval($type4[0])) {
			$this->SetLineWidth(floatval($type4[0]));
			$this->Line($x , $y , $x2, $y );
		}
		if (floatval($type4[1])) {
			$this->SetLineWidth(floatval($type4[1]));
			$this->Line($x2, $y , $x2, $y2);
		}
		if (floatval($type4[2])) {
			$this->SetLineWidth(floatval($type4[2]));
			$this->Line($x , $y2, $x2, $y2);
		}
		if (floatval($type4[3])) {
			$this->SetLineWidth(floatval($type4[3]));
			$this->Line($x , $y , $x , $y2);
		}
		$this->SetLineWidth($width);
	}
	elseif ((int)$type===1)
		$this->Rect($x, $y, $w, $h);
	elseif ((int)$type>1 && (int)$type<11)
	{
		$width = $this->LineWidth;
		$this->SetLineWidth($type * $this->LineWidth);
		$this->Rect($x, $y, $w, $h);
		$this->SetLineWidth($width);
	}
}

private function _tableDrawBorder(&$table){
	//When fill a cell, it overwrite the border of prevous cell, then I have to draw border at the end
	foreach ($table['listborder'] as $c){
		list($x,$y,$w,$h,$s) = $c;
		$this->_tableRect($x,$y,$w,$h,$s);
	}

	$table['listborder'] = array();
}
private function _tableWriteRow(&$table,$i,$x0){
	$maxh = 0;
	for ($j=0;$j<$table['nc'];$j++){
		$h = $this->_tableGetHCell($table, $i, $j);
		if ($maxh < $h) $maxh = $h;
	}
	if ($table['lasty']+$maxh>$this->bottom && $table['multipage']){
		if ($maxh+$table['repeatH'] > $this->height){
			$msg = 'Height of this row is greater than page height!';
			$this->SetFillColor(255,0,0);
			$h=$this->bottom-$table['lasty'];
			$this->Rect($this->x, $this->y=$table['lasty'], $table['w'], $h, 'F');
			$this->MultiCell($table['w'],$h,$msg);
			$table['lasty'] += $h;
			return ;
		}
		$this->_tableDrawBorder($table);
		$this->AddPage($this->CurOrientation);

		$table['lasty'] = $this->y;
		if ($table['repeat']){
			foreach ($table['repeat'] as $r){
				if ($r==$i) continue;
				$this->_tableWriteRow($table,$r,$x0);
			}
		}
	}
	$y = $table['lasty'];
	for ($j=0;$j<$table['nc'];$j++){
		if (isset($table['cells'][$i][$j]['miw'])){
			$c = &$table['cells'][$i][$j];
			list($x,$w) = $this->_tableGetWCell($table, $i, $j);
			$h = $this->_tableGetHCell($table, $i, $j);
			$x += $x0;
			
			//Fill
			$fill = isset($c['bgcolor']) ? $c['bgcolor']
				: (isset($table['bgcolor'][$i]) ? $table['bgcolor'][$i]
				: (isset($table['bgcolor'][-1]) ? $table['bgcolor'][-1] : 0));
			if ($fill){
				 if (strchr($fill,'rgb')){
                	$fill = str_replace('rgb(','',$fill);
                	$fill = str_replace(')','',$fill);
                	$color = explode(',',$fill);
                	
                }
                else{
            		$color = Color::HEX2RGB($fill);
                }
				
				$this->SetFillColor($color[0],$color[1],$color[2]);
				$this->Rect($x, $y, $w, $h, 'F');
			};
			//Content
			if (isset($c['img'])){
//----------------------------------------------------------------- Center Align
				$img_x = $x + (($w / 2) - ($c['w'] / 2));
				$this->Image($c['img'],$img_x,$y,$c['w'],$c['h']);
			}else{
				$this->_drawCellAligned($x,$y,$c);
			}
			//Border
			if (isset($c['border'])){
				$table['listborder'][] = array($x,$y,$w,$h,$c['border']);
			}elseif (isset($table['border']) && $table['border'])
				$table['listborder'][] = array($x,$y,$w,$h,$table['border']);
		}
	}
	$table['lasty'] += $table['hr'][$i];
	$this->y = $table['lasty'];
}
function SetFont($family, $style='', $size=0, $default=false)
{
	parent::SetFont($family, $style, $size);
	if ($default||$this->isNotYetSetFont){
		$this->defaultFontFamily = $family;
		$this->defaultFontSize = $size;
		$this->defaultFontStyle = $style;
		$this->isNotYetSetFont = false;
	}
}
private function _setFontText(&$f){
	if (isset($f['size']) && ($f['size'] >0)){
		$fontSize   = $f['size'];
	}else $fontSize   = $this->defaultFontSize;
	if (isset($f['family'])){
		$fontFamily = $f['family'];
	}else $fontFamily = $this->defaultFontFamily;
	if (isset($f['style']))
		$fontStyle  = $f['style'];
	else $fontStyle = $this->defaultFontStyle;
	$this->SetFont($fontFamily, $fontStyle, $fontSize);
	return $fontSize;
}
private function _tableWrite(&$table){
	if ($this->CurOrientation == 'P' && $table['w']>$this->width+5){
		$this->AddPage('L');
	}
	if ($this->x==null)$this->x = $this->lMargin;
	if ($this->y==null)$this->y = $this->tMargin;
	$x0 = $this->x;
	$y0 = $this->y;
	if (isset($table['a'])){
		if ($table['a']=='C'){
			$x0 += (($this->right-$x0) - $table['w'])/2;
		}elseif ($table['a']=='R'){
			$x0 = $this->right - $table['w'];
		}
	}
	if (isset($table['nobreak'])&&$table['nobreak']
		&& $table['tth']+$y0>$this->bottom && $table['multipage']){
		$this->AddPage($this->CurOrientation);
		$table['lasty'] = $this->y;
	}else
		$table['lasty'] = $y0;
	
	$table['listborder'] = array();
	for ($i=0;$i<$table['nr'];$i++) $this->_tableWriteRow($table, $i, $x0);
	$this->_tableDrawBorder($table);
	$this->x = $x0;
}

function htmltable(&$html,$multipage=1){
	$a = $this->AutoPageBreak;
	$this->SetAutoPageBreak(0,$this->bMargin);
	$HTML = explode("<table", $html);
	foreach ($HTML as $i=>$table)
	{
		if (strlen($table)<6) continue;
		$table = '<table' . $table;
		$table = $this->_tableParser($table);
		$table['multipage'] = $multipage;
		$this->_tableColumnWidth($table);
		$this->_tableWidth($table);
		$this->_tableHeight($table);
		$this->_tableWrite($table);
	}
	$this->SetAutoPageBreak($a,$this->bMargin);
}
function _putinfo()
{
	$this->_out('/Producer '.$this->_textstring('PDFTable '.
		PDFTABLE_VERSION.' based on FPDF '.FPDF_VERSION));
	if(!empty($this->title))
		$this->_out('/Title '.$this->_textstring($this->title));
	if(!empty($this->subject))
		$this->_out('/Subject '.$this->_textstring($this->subject));
	if(!empty($this->author))
		$this->_out('/Author '.$this->_textstring($this->author));
	if(!empty($this->keywords))
		$this->_out('/Keywords '.$this->_textstring($this->keywords));
	if(!empty($this->creator))
		$this->_out('/Creator '.$this->_textstring($this->creator));
	$this->_out('/CreationDate '.$this->_textstring('D:'.@date('YmdHis')));
}
private function getAlign($v){
	global $PDF_ALIGN;
	$v = strtolower($v);
	return isset($PDF_ALIGN[$v])?$PDF_ALIGN[$v]:'L';
}
private function getVAlign($v){
	global $PDF_VALIGN;
	$v = strtolower($v);
	return isset($PDF_VALIGN[$v])?$PDF_VALIGN[$v]:'T';
}

}
?>