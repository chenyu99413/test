<?php
//
//  FPDI - Version 1.3.1
//
//    Copyright 2004-2009 Setasign - Jan Slabon
//
//  Licensed under the Apache License, Version 2.0 (the "License");
//  you may not use this file except in compliance with the License.
//  You may obtain a copy of the License at
//
//      http://www.apache.org/licenses/LICENSE-2.0
//
//  Unless required by applicable law or agreed to in writing, software
//  distributed under the License is distributed on an "AS IS" BASIS,
//  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//  See the License for the specific language governing permissions and
//  limitations under the License.
//

define('FPDI_VERSION','1.3.1');

// Check for TCPDF and remap TCPDF to FPDF
if (class_exists('TCPDF')) {
    require_once('fpdi2tcpdf_bridge.php');
}

require_once('fpdf_tpl.php');
require_once('fpdi_pdf_parser.php');

$Big5_widths = array(' '=>250,'!'=>250,'"'=>408,'#'=>668,'$'=>490,'%'=>875,'&'=>698,'\''=>250,
    '('=>240,')'=>240,'*'=>417,'+'=>667,','=>250,'-'=>313,'.'=>250,'/'=>520,'0'=>500,'1'=>500,
    '2'=>500,'3'=>500,'4'=>500,'5'=>500,'6'=>500,'7'=>500,'8'=>500,'9'=>500,':'=>250,';'=>250,
    '<'=>667,'='=>667,'>'=>667,'?'=>396,'@'=>921,'A'=>677,'B'=>615,'C'=>719,'D'=>760,'E'=>625,
    'F'=>552,'G'=>771,'H'=>802,'I'=>354,'J'=>354,'K'=>781,'L'=>604,'M'=>927,'N'=>750,'O'=>823,
    'P'=>563,'Q'=>823,'R'=>729,'S'=>542,'T'=>698,'U'=>771,'V'=>729,'W'=>948,'X'=>771,'Y'=>677,
    'Z'=>635,'['=>344,'\\'=>520,']'=>344,'^'=>469,'_'=>500,'`'=>250,'a'=>469,'b'=>521,'c'=>427,
    'd'=>521,'e'=>438,'f'=>271,'g'=>469,'h'=>531,'i'=>250,'j'=>250,'k'=>458,'l'=>240,'m'=>802,
    'n'=>531,'o'=>500,'p'=>521,'q'=>521,'r'=>365,'s'=>333,'t'=>292,'u'=>521,'v'=>458,'w'=>677,
    'x'=>479,'y'=>458,'z'=>427,'{'=>480,'|'=>496,'}'=>480,'~'=>667);
$GB_widths = array(''=>0,' '=>207,'!'=>270,'"'=>342,'#'=>467,'$'=>462,'%'=>797,'&'=>710,'\''=>239,
    '('=>374,')'=>374,'*'=>423,'+'=>605,','=>238,'-'=>375,'.'=>238,'/'=>334,'0'=>462,'1'=>462,
    '2'=>462,'3'=>462,'4'=>462,'5'=>462,'6'=>462,'7'=>462,'8'=>462,'9'=>462,':'=>238,';'=>238,
    '<'=>605,'='=>605,'>'=>605,'?'=>344,'@'=>748,'A'=>684,'B'=>560,'C'=>695,'D'=>739,'E'=>563,
    'F'=>511,'G'=>729,'H'=>793,'I'=>318,'J'=>312,'K'=>666,'L'=>526,'M'=>896,'N'=>758,'O'=>772,
    'P'=>544,'Q'=>772,'R'=>628,'S'=>465,'T'=>607,'U'=>753,'V'=>711,'W'=>972,'X'=>647,'Y'=>620,
    'Z'=>607,'['=>374,'\\'=>333,']'=>374,'^'=>606,'_'=>500,'`'=>239,'a'=>417,'b'=>503,'c'=>427,
    'd'=>529,'e'=>415,'f'=>264,'g'=>444,'h'=>518,'i'=>241,'j'=>230,'k'=>495,'l'=>228,'m'=>793,
    'n'=>527,'o'=>524,'p'=>524,'q'=>504,'r'=>338,'s'=>336,'t'=>277,'u'=>517,'v'=>450,'w'=>652,
    'x'=>466,'y'=>452,'z'=>407,'{'=>370,'|'=>258,'}'=>370,'~'=>605);
class FPDI extends FPDF_TPL {
    /**
     * Actual filename
     * @var string
     */
    var $current_filename;

    /**
     * Parser-Objects
     * @var array
     */
    var $parsers;
    
    /**
     * Current parser
     * @var object
     */
    var $current_parser;
    
    /**
     * object stack
     * @var array
     */
    var $_obj_stack;
    
    /**
     * done object stack
     * @var array
     */
    var $_don_obj_stack;

    /**
     * Current Object Id.
     * @var integer
     */
    var $_current_obj_id;
    
    /**
     * The name of the last imported page box
     * @var string
     */
    var $lastUsedPageBox;
    
    var $_importedPages = array();
    
    
    /**
     * Set a source-file
     *
     * @param string $filename a valid filename
     * @return int number of available pages
     */
    function setSourceFile($filename) {
        $this->current_filename = $filename;
        $fn =& $this->current_filename;

        if (!isset($this->parsers[$fn]))
            $this->parsers[$fn] =& new fpdi_pdf_parser($fn, $this);
        $this->current_parser =& $this->parsers[$fn];
        
        return $this->parsers[$fn]->getPageCount();
    }
    
    /**
     * Import a page
     *
     * @param int $pageno pagenumber
     * @return int Index of imported page - to use with fpdf_tpl::useTemplate()
     */
    function importPage($pageno, $angle=false,$boxName='/CropBox') {
        if ($this->_intpl) {
            return $this->error('Please import the desired pages before creating a new template.');
        }
        
        $fn =& $this->current_filename;
        
        // check if page already imported
        $pageKey = $fn.((int)$pageno).$boxName;
        if (isset($this->_importedPages[$pageKey]))
            return $this->_importedPages[$pageKey];
        
        $parser =& $this->parsers[$fn];
        $parser->setPageno($pageno);

        $this->tpl++;
        $this->tpls[$this->tpl] = array();
        $tpl =& $this->tpls[$this->tpl];
        $tpl['parser'] =& $parser;
        $tpl['resources'] = $parser->getPageResources();
        $tpl['buffer'] = $parser->getContent();
        
        if (!in_array($boxName, $parser->availableBoxes))
            return $this->Error(sprintf('Unknown box: %s', $boxName));
        $pageboxes = $parser->getPageBoxes($pageno);
        
        /**
         * MediaBox
         * CropBox: Default -> MediaBox
         * BleedBox: Default -> CropBox
         * TrimBox: Default -> CropBox
         * ArtBox: Default -> CropBox
         */
        if (!isset($pageboxes[$boxName]) && ($boxName == '/BleedBox' || $boxName == '/TrimBox' || $boxName == '/ArtBox'))
            $boxName = '/CropBox';
        if (!isset($pageboxes[$boxName]) && $boxName == '/CropBox')
            $boxName = '/MediaBox';
        
        if (!isset($pageboxes[$boxName]))
            return false;
        $this->lastUsedPageBox = $boxName;
        
        $box = $pageboxes[$boxName];
        $tpl['box'] = $box;
        
        // To build an array that can be used by PDF_TPL::useTemplate()
        $this->tpls[$this->tpl] = array_merge($this->tpls[$this->tpl],$box);
        
        // An imported page will start at 0,0 everytime. Translation will be set in _putformxobjects()
        $tpl['x'] = 0;
        $tpl['y'] = 0;
        
        $page =& $parser->pages[$parser->pageno];
        
        // handle rotated pages
        $rotation = $parser->getPageRotation($pageno);
        $tpl['_rotationAngle'] = 0;
        if ((isset($rotation[1]) && ($angle = $rotation[1] % 360) != 0) || $angle) {
            $steps = $angle / 90;
                
            $_w = $tpl['w'];
            $_h = $tpl['h'];
            $tpl['w'] = $steps % 2 == 0 ? $_w : $_h;
            $tpl['h'] = $steps % 2 == 0 ? $_h : $_w;
            
            $tpl['_rotationAngle'] = $angle*-1;
        }
        
        $this->_importedPages[$pageKey] = $this->tpl;
        
        return $this->tpl;
    }
    
    function getLastUsedPageBox() {
        return $this->lastUsedPageBox;
    }
    
    function useTemplate($tplidx, $_x=null, $_y=null, $_w=0, $_h=0, $adjustPageSize=false) {
        if ($adjustPageSize == true && is_null($_x) && is_null($_y)) {
            $size = $this->getTemplateSize($tplidx, $_w, $_h);
            $format = array($size['w'], $size['h']);
            if ($format[0]!=$this->CurPageFormat[0] || $format[1]!=$this->CurPageFormat[1]) {
                $this->w=$format[0];
                $this->h=$format[1];
                $this->wPt=$this->w*$this->k;
        		$this->hPt=$this->h*$this->k;
        		$this->PageBreakTrigger=$this->h-$this->bMargin;
        		$this->CurPageFormat=$format;
        		$this->PageSizes[$this->page]=array($this->wPt, $this->hPt);
            }
        }
        
        $this->_out('q 0 J 1 w 0 j 0 G 0 g'); // reset standard values
        $s = parent::useTemplate($tplidx, $_x, $_y, $_w, $_h);
        $this->_out('Q');
        return $s;
    }
    
    /**
     * Private method, that rebuilds all needed objects of source files
     */
    function _putimportedobjects() {
        if (is_array($this->parsers) && count($this->parsers) > 0) {
            foreach($this->parsers AS $filename => $p) {
                $this->current_parser =& $this->parsers[$filename];
                if (isset($this->_obj_stack[$filename]) && is_array($this->_obj_stack[$filename])) {
                    while(($n = key($this->_obj_stack[$filename])) !== null) {
                        $nObj = $this->current_parser->pdf_resolve_object($this->current_parser->c,$this->_obj_stack[$filename][$n][1]);
						
                        $this->_newobj($this->_obj_stack[$filename][$n][0]);
                        
                        if ($nObj[0] == PDF_TYPE_STREAM) {
							$this->pdf_write_value ($nObj);
                        } else {
                            $this->pdf_write_value ($nObj[1]);
                        }
                        
                        $this->_out('endobj');
                        $this->_obj_stack[$filename][$n] = null; // free memory
                        unset($this->_obj_stack[$filename][$n]);
                        reset($this->_obj_stack[$filename]);
                    }
                }
            }
        }
    }
    
    
    /**
     * Private Method that writes the form xobjects
     */
    function _putformxobjects() {
        $filter=($this->compress) ? '/Filter /FlateDecode ' : '';
	    reset($this->tpls);
        foreach($this->tpls AS $tplidx => $tpl) {
            $p=($this->compress) ? gzcompress($tpl['buffer']) : $tpl['buffer'];
    		$this->_newobj();
    		$cN = $this->n; // TCPDF/Protection: rem current "n"
    		
    		$this->tpls[$tplidx]['n'] = $this->n;
    		$this->_out('<<'.$filter.'/Type /XObject');
            $this->_out('/Subtype /Form');
            $this->_out('/FormType 1');
            
            $this->_out(sprintf('/BBox [%.2F %.2F %.2F %.2F]', 
                (isset($tpl['box']['llx']) ? $tpl['box']['llx'] : $tpl['x'])*$this->k,
                (isset($tpl['box']['lly']) ? $tpl['box']['lly'] : -$tpl['y'])*$this->k,
                (isset($tpl['box']['urx']) ? $tpl['box']['urx'] : $tpl['w'] + $tpl['x'])*$this->k,
                (isset($tpl['box']['ury']) ? $tpl['box']['ury'] : $tpl['h']-$tpl['y'])*$this->k
            ));
            
            $c = 1;
            $s = 0;
            $tx = 0;
            $ty = 0;
            
            if (isset($tpl['box'])) {
                $tx = -$tpl['box']['llx'];
                $ty = -$tpl['box']['lly']; 
                
                if ($tpl['_rotationAngle'] <> 0) {
                    $angle = $tpl['_rotationAngle'] * M_PI/180;
                    $c=cos($angle);
                    $s=sin($angle);
                    
                    switch($tpl['_rotationAngle']) {
                        case -90:
                           $tx = -$tpl['box']['lly'];
                           $ty = $tpl['box']['urx'];
                           break;
                        case -180:
                            $tx = $tpl['box']['urx'];
                            $ty = $tpl['box']['ury'];
                            break;
                        case -270:
                            $tx = $tpl['box']['ury'];
                            $ty = 0;
                            break;
                    }
                }
            } else if ($tpl['x'] != 0 || $tpl['y'] != 0) {
                $tx = -$tpl['x']*2;
                $ty = $tpl['y']*2;
            }
            
            $tx *= $this->k;
            $ty *= $this->k;
            
            if ($c != 1 || $s != 0 || $tx != 0 || $ty != 0) {
                $this->_out(sprintf('/Matrix [%.5F %.5F %.5F %.5F %.5F %.5F]',
                    $c, $s, -$s, $c, $tx, $ty
                ));
            }
            
            $this->_out('/Resources ');

            if (isset($tpl['resources'])) {
                $this->current_parser =& $tpl['parser'];
                $this->pdf_write_value($tpl['resources']); // "n" will be changed
            } else {
                $this->_out('<</ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
            	if (isset($this->_res['tpl'][$tplidx]['fonts']) && count($this->_res['tpl'][$tplidx]['fonts'])) {
                	$this->_out('/Font <<');
                    foreach($this->_res['tpl'][$tplidx]['fonts'] as $font)
                		$this->_out('/F'.$font['i'].' '.$font['n'].' 0 R');
                	$this->_out('>>');
                }
            	if(isset($this->_res['tpl'][$tplidx]['images']) && count($this->_res['tpl'][$tplidx]['images']) || 
            	   isset($this->_res['tpl'][$tplidx]['tpls']) && count($this->_res['tpl'][$tplidx]['tpls']))
            	{
                    $this->_out('/XObject <<');
                    if (isset($this->_res['tpl'][$tplidx]['images']) && count($this->_res['tpl'][$tplidx]['images'])) {
                        foreach($this->_res['tpl'][$tplidx]['images'] as $image)
                  			$this->_out('/I'.$image['i'].' '.$image['n'].' 0 R');
                    }
                    if (isset($this->_res['tpl'][$tplidx]['tpls']) && count($this->_res['tpl'][$tplidx]['tpls'])) {
                        foreach($this->_res['tpl'][$tplidx]['tpls'] as $i => $tpl)
                            $this->_out($this->tplprefix.$i.' '.$tpl['n'].' 0 R');
                    }
                    $this->_out('>>');
            	}
            	$this->_out('>>');
            }

            $nN = $this->n; // TCPDF: rem new "n"
            $this->n = $cN; // TCPDF: reset to current "n"
            $this->_out('/Length '.strlen($p).' >>');
    		$this->_putstream($p);
    		$this->_out('endobj');
    		$this->n = $nN; // TCPDF: reset to new "n"
        }
        
        $this->_putimportedobjects();
    }

    /**
     * Rewritten to handle existing own defined objects
     */
    function _newobj($obj_id=false,$onlynewobj=false) {
        if (!$obj_id) {
            $obj_id = ++$this->n;
        }

        //Begin a new object
        if (!$onlynewobj) {
            $this->offsets[$obj_id] = is_subclass_of($this, 'TCPDF') ? $this->bufferlen : strlen($this->buffer);
            $this->_out($obj_id.' 0 obj');
            $this->_current_obj_id = $obj_id; // for later use with encryption
        }
        return $obj_id;
    }

    /**
     * Writes a value
     * Needed to rebuild the source document
     *
     * @param mixed $value A PDF-Value. Structure of values see cases in this method
     */
    function pdf_write_value(&$value)
    {
        if (is_subclass_of($this, 'TCPDF')) {
            parent::pdf_write_value($value);
        }
        
        switch ($value[0]) {

    		case PDF_TYPE_TOKEN :
                $this->_straightOut($value[1] . ' ');
    			break;
		    case PDF_TYPE_NUMERIC :
    		case PDF_TYPE_REAL :
                if (is_float($value[1]) && $value[1] != 0) {
    			    $this->_straightOut(rtrim(rtrim(sprintf('%F', $value[1]), '0'), '.') .' ');
    			} else {
        			$this->_straightOut($value[1] . ' ');
    			}
    			break;
    			
    		case PDF_TYPE_ARRAY :

    			// An array. Output the proper
    			// structure and move on.

    			$this->_straightOut('[');
                for ($i = 0; $i < count($value[1]); $i++) {
    				$this->pdf_write_value($value[1][$i]);
    			}

    			$this->_out(']');
    			break;

    		case PDF_TYPE_DICTIONARY :

    			// A dictionary.
    			$this->_straightOut('<<');

    			reset ($value[1]);

    			while (list($k, $v) = each($value[1])) {
    				$this->_straightOut($k . ' ');
    				$this->pdf_write_value($v);
    			}

    			$this->_straightOut('>>');
    			break;

    		case PDF_TYPE_OBJREF :

    			// An indirect object reference
    			// Fill the object stack if needed
    			$cpfn =& $this->current_parser->filename;
    			
    			if (!isset($this->_don_obj_stack[$cpfn][$value[1]])) {
    			    $this->_newobj(false,true);
    			    $this->_obj_stack[$cpfn][$value[1]] = array($this->n, $value);
                    $this->_don_obj_stack[$cpfn][$value[1]] = array($this->n, $value); // Value is maybee obsolete!!!
                }
                $objid = $this->_don_obj_stack[$cpfn][$value[1]][0];

    			$this->_out($objid.' 0 R');
    			break;

    		case PDF_TYPE_STRING :

    			// A string.
                $this->_straightOut('('.$value[1].')');

    			break;

    		case PDF_TYPE_STREAM :

    			// A stream. First, output the
    			// stream dictionary, then the
    			// stream data itself.
                $this->pdf_write_value($value[1]);
    			$this->_out('stream');
    			$this->_out($value[2][1]);
    			$this->_out('endstream');
    			break;
            case PDF_TYPE_HEX :
                $this->_straightOut('<'.$value[1].'>');
                break;

            case PDF_TYPE_BOOLEAN :
    		    $this->_straightOut($value[1] ? 'true ' : 'false ');
    		    break;
            
    		case PDF_TYPE_NULL :
                // The null object.

    			$this->_straightOut('null ');
    			break;
    	}
    }
    
    
    /**
     * Modified so not each call will add a newline to the output.
     */
    function _straightOut($s) {
        if (!is_subclass_of($this, 'TCPDF')) {
            if($this->state==2)
        		$this->pages[$this->page] .= $s;
        	else
        		$this->buffer .= $s;
        } else {
            if ($this->state == 2) {
				if (isset($this->footerlen[$this->page]) AND ($this->footerlen[$this->page] > 0)) {
					// puts data before page footer
					$page = substr($this->getPageBuffer($this->page), 0, -$this->footerlen[$this->page]);
					$footer = substr($this->getPageBuffer($this->page), -$this->footerlen[$this->page]);
					$this->setPageBuffer($this->page, $page.' '.$s."\n".$footer);
				} else {
					$this->setPageBuffer($this->page, $s, true);
				}
			} else {
				$this->setBuffer($s);
			}
        }
    }

    /**
     * rewritten to close opened parsers
     *
     */
    function _enddoc() {
        parent::_enddoc();
        $this->_closeParsers();
    }
    
    /**
     * close all files opened by parsers
     */
    function _closeParsers() {
        if ($this->state > 2 && count($this->parsers) > 0) {
          	foreach ($this->parsers as $k => $_){
            	$this->parsers[$k]->closeFile();
            	$this->parsers[$k] = null;
            	unset($this->parsers[$k]);
            }
            return true;
        }
        return false;
    }


    function AddCIDFont($family, $style, $name, $cw, $CMap, $registry)
    {
        $fontkey = strtolower($family).strtoupper($style);
        if(isset($this->fonts[$fontkey]))
            $this->Error("Font already added: $family $style");
        $i = count($this->fonts)+1;
        $name = str_replace(' ','',$name);
        $this->fonts[$fontkey] = array('i'=>$i, 'type'=>'Type0', 'name'=>$name, 'up'=>-130, 'ut'=>40, 'cw'=>$cw, 'CMap'=>$CMap, 'registry'=>$registry);
    }
    function AddCIDFonts($family, $name, $cw, $CMap, $registry)
    {
        $this->AddCIDFont($family,'',$name,$cw,$CMap,$registry);
        $this->AddCIDFont($family,'B',$name.',Bold',$cw,$CMap,$registry);
        $this->AddCIDFont($family,'I',$name.',Italic',$cw,$CMap,$registry);
        $this->AddCIDFont($family,'BI',$name.',BoldItalic',$cw,$CMap,$registry);
    }
    function AddBig5Font($family='Big5', $name='MSungStd-Light-Acro')
    {
        // Add Big5 font with proportional Latin
        $cw = $GLOBALS['Big5_widths'];
        $CMap = 'ETenms-B5-H';
        $registry = array('ordering'=>'CNS1', 'supplement'=>0);
        $this->AddCIDFonts($family,$name,$cw,$CMap,$registry);
    }
    function AddBig5hwFont($family='Big5-hw', $name='MSungStd-Light-Acro')
    {
        // Add Big5 font with half-witdh Latin
        for($i=32;$i<=126;$i++)
            $cw[chr($i)] = 500;
            $CMap = 'ETen-B5-H';
            $registry = array('ordering'=>'CNS1', 'supplement'=>0);
            $this->AddCIDFonts($family,$name,$cw,$CMap,$registry);
    }
    function AddGBFont($family='GB', $name='STSongStd-Light-Acro')
    {
    // Add GB font with proportional Latin
    $cw = array(''=>480,' '=>480,'!'=>480,'"'=>480,'#'=>480,'$'=>480,'%'=>480,'&'=>480,'\''=>480,
    '('=>480,')'=>480,'*'=>480,'+'=>480,','=>480,'-'=>480,'.'=>480,'/'=>480,'0'=>480,'1'=>480,
    '2'=>480,'3'=>480,'4'=>480,'5'=>480,'6'=>480,'7'=>480,'8'=>480,'9'=>480,':'=>480,';'=>480,
    '<'=>480,'='=>480,'>'=>480,'?'=>480,'@'=>480,'A'=>480,'B'=>480,'C'=>480,'D'=>480,'E'=>480,
    'F'=>480,'G'=>480,'H'=>480,'I'=>480,'J'=>480,'K'=>480,'L'=>480,'M'=>480,'N'=>480,'O'=>480,
    'P'=>480,'Q'=>480,'R'=>480,'S'=>480,'T'=>480,'U'=>480,'V'=>480,'W'=>480,'X'=>480,'Y'=>480,
    'Z'=>480,'['=>480,'\\'=>480,']'=>480,'^'=>480,'_'=>480,'`'=>480,'a'=>480,'b'=>480,'c'=>480,
    'd'=>480,'e'=>480,'f'=>480,'g'=>480,'h'=>480,'i'=>480,'j'=>480,'k'=>480,'l'=>480,'m'=>480,
        'n'=>480,'o'=>480,'p'=>480,'q'=>480,'r'=>480,'s'=>480,'t'=>480,'u'=>480,'v'=>480,'w'=>480,
        'x'=>480,'y'=>480,'z'=>480,'{'=>480,'|'=>480,'}'=>480,'~'=>480);
    $CMap = 'GBKp-EUC-H';
	$registry = array('ordering'=>'GB1', 'supplement'=>2);
    	$this->AddCIDFonts($family,$name,$cw,$CMap,$registry);
    }
    function AddGBhwFont($family='GB-hw', $name='STSongStd-Light-Acro')
        {
        // Add GB font with half-width Latin
        for($i=32;$i<=126;$i++)
		$cw[chr($i)] = 500;
    		$CMap = 'GBK-EUC-H';
    		$registry = array('ordering'=>'GB1', 'supplement'=>2);
    		$this->AddCIDFonts($family,$name,$cw,$CMap,$registry);
        }
        function GetStringWidth($s)
        {
        if($this->CurrentFont['type']=='Type0')
            return $this->GetMBStringWidth($s);
            else
                return parent::GetStringWidth($s);
                }
                function GetMBStringWidth($s)
                {
                // 	Multi-byte version of GetStringWidth()
                $l = 0;
                $cw = &$this->CurrentFont['cw'];
                $nb = strlen($s);
                $i = 0;
                while($i<$nb)
                {
                $c = $s[$i];
                if(ord($c)<128)
                {
                // 		    if(!isset($cw[$c])){
                // 		      echo $c;exit();
                // 		    }
                $l += $cw[$c];
    
                $i++;
                }
                else
                {
                $l += 1000;
                $i += 2;
    }
    }
    return $l*$this->FontSize/1000;
    }
    function MultiCell($w, $h, $txt, $border=0, $align='L', $fill=0)
                    {
                    if($this->CurrentFont['type']=='Type0')
                    $this->MBMultiCell($w,$h,$txt,$border,$align,$fill);
                    else
                        parent::MultiCell($w,$h,$txt,$border,$align,$fill);
                }
                function MBMultiCell($w, $h, $txt, $border=0, $align='L', $fill=0)
                {
                // Multi-byte version of MultiCell()
                $cw = &$this->CurrentFont['cw'];
                if($w==0)
                    $w = $this->w-$this->rMargin-$this->x;
                    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
                    $s = str_replace("\r",'',$txt);
                        $nb = strlen($s);
                        if($nb>0 && $s[$nb-1]=="\n")
                            $nb--;
                                $b = 0;
                                if($border)
                                {
                                if($border==1)
                                {
                                $border = 'LTRB';
                                $b = 'LRT';
                                $b2 = 'LR';
                        }
                        else
                        {
                        $b2 = '';
                        if(is_int(strpos($border,'L')))
                            $b2 .= 'L';
                            if(is_int(strpos($border,'R')))
                                $b2 .= 'R';
                            $b = is_int(strpos($border,'T')) ? $b2.'T' : $b2;
                            }
	}
                            $sep = -1;
                            $i = 0;
                            $j = 0;
                            $l = 0;
                            $nl = 1;
                            while($i<$nb)
                            {
                            // Get next character
                            $c = $s[$i];
                            // Check if ASCII or MB
                            $ascii = (ord($c)<128);
                            if($c=="\n")
                            {
                            // Explicit line break
                        $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
                        $i++;
                        $sep = -1;
                        $j = $i;
                        $l = 0;
                        $nl++;
                        if($border && $nl==2)
                            $b = $b2;
                        continue;
                        }
                        if(!$ascii)
                        {
                            $sep = $i;
                            $ls = $l;
                            }
                            elseif($c==' ')
                            {
                            $sep = $i;
                                $ls = $l;
                            }
                            $l += $ascii ? $cw[$c] : 1000;
                            if($l>$wmax)
                            {
                                // Automatic line break
                                if($sep==-1 || $i==$j)
                                    {
                                    if($i==$j)
                                        $i += $ascii ? 1 : 2;
                                            $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
                                        }
                                        else
                                        {
                                        $this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
                                        $i = ($s[$sep]==' ') ? $sep+1 : $sep;
                                        }
                                        $sep = -1;
                                        $j = $i;
                                        $l = 0;
                                        $nl++;
                                        if($border && $nl==2)
                                            $b = $b2;
                        }
                        else
                            $i += $ascii ? 1 : 2;
                            }
                            // Last chunk
                            if($border && is_int(strpos($border,'B')))
                            $b .= 'B';
	$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
    	$this->x = $this->lMargin;
}
function Write($h, $txt, $link='')
    {
    if($this->CurrentFont['type']=='Type0')
    	$this->MBWrite($h,$txt,$link);
    	else
    	    parent::Write($h,$txt,$link);
}
function MBWrite($h, $txt, $link)
{
	// Multi-byte version of Write()
    	$cw = &$this->CurrentFont['cw'];
    	$w = $this->w-$this->rMargin-$this->x;
    	$wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
	$s = str_replace("\r",'',$txt);
	$nb = strlen($s);
    	$sep = -1;
	$i = 0;
	$j = 0;
    	$l = 0;
    	$nl = 1;
    	while($i<$nb)
    	{
		// Get next character
		$c = $s[$i];
    	// Check if ASCII or MB
		$ascii = (ord($c)<128);
		if($c=="\n")
    	{
    	// Explicit line break
    	$this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
    	$i++;
    	$sep = -1;
    	$j = $i;
    	$l = 0;
    	if($nl==1)
    	{
    	$this->x = $this->lMargin;
				$w = $this->w-$this->rMargin-$this->x;
    				$wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
                            }
    				    $nl++;
    				    continue;
                            }
		if(!$ascii || $c==' ')
			$sep = $i;
    				    $l += $ascii ? $cw[$c] : 1000;
    				    if($l>$wmax)
		{
    				        // Automatic line break
    			if($sep==-1 || $i==$j)
    			{
    				if($this->x>$this->lMargin)
    				{
    					// Move to next line
    					$this->x = $this->lMargin;
    					$this->y += $h;
    					$w = $this->w-$this->rMargin-$this->x;
    					$wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
    					$i++;
    					$nl++;
    					continue;
    				}
    				if($i==$j)
    					$i += $ascii ? 1 : 2;
    				$this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
    			}
    			else
    			{
    				$this->Cell($w,$h,substr($s,$j,$sep-$j),0,2,'',0,$link);
    				$i = ($s[$sep]==' ') ? $sep+1 : $sep;
    			}
    			$sep = -1;
    			$j = $i;
    			$l = 0;
    			if($nl==1)
    			{
    				$this->x = $this->lMargin;
    				$w = $this->w-$this->rMargin-$this->x;
    				$wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
    			}
    			$nl++;
    		}
    		else
    			$i += $ascii ? 1 : 2;
    	}
    	// Last chunk
    	if($i!=$j)
    		$this->Cell($l/1000*$this->FontSize,$h,substr($s,$j,$i-$j),0,0,'',0,$link);
    }
    function _putType0($font)
    {
    	// Type0
    	$this->_newobj();
    	$this->_out('<</Type /Font');
    	$this->_out('/Subtype /Type0');
    	$this->_out('/BaseFont /'.$font['name'].'-'.$font['CMap']);
    	$this->_out('/Encoding /'.$font['CMap']);
    	$this->_out('/DescendantFonts ['.($this->n+1).' 0 R]');
    	$this->_out('>>');
    	$this->_out('endobj');
    	// CIDFont
    	$this->_newobj();
    	$this->_out('<</Type /Font');
    	$this->_out('/Subtype /CIDFontType0');
    	$this->_out('/BaseFont /'.$font['name']);
    	$this->_out('/CIDSystemInfo <</Registry '.$this->_textstring('Adobe').' /Ordering '.$this->_textstring($font['registry']['ordering']).' /Supplement '.$font['registry']['supplement'].'>>');
    	$this->_out('/FontDescriptor '.($this->n+1).' 0 R');
    	if($font['CMap']=='ETen-B5-H')
    		$W = '13648 13742 500';
    	elseif($font['CMap']=='GBK-EUC-H')
    		$W = '814 907 500 7716 [500]';
    	else
    		$W = '1 ['.implode(' ',$font['cw']).']';
    	$this->_out('/W ['.$W.']>>');
    	$this->_out('endobj');
    	// Font descriptor
    	$this->_newobj();
    	$this->_out('<</Type /FontDescriptor');
    	$this->_out('/FontName /'.$font['name']);
    	$this->_out('/Flags 6');
    	$this->_out('/FontBBox [0 -200 1000 900]');
    	$this->_out('/ItalicAngle 0');
    	$this->_out('/Ascent 800');
    	$this->_out('/Descent -200');
    	$this->_out('/CapHeight 800');
    	$this->_out('/StemV 50');
    	$this->_out('>>');
    	$this->_out('endobj');
    }
    
}