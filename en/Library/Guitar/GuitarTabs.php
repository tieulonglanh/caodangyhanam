<?php
/*!
 * Guitar Tabs PHP Class
 * http://www.littlewebthings.com/projects/guitarTabs
 *
 * Copyright 2010, Vassilis Dourdounis (me@littlewebthings.com)
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
class Guitar_GuitarTabs {

	public $lyrics;
	public $tabs;
    public $key;
	function __contruct($l=null) {
		$this->lyrics = array();
		$this->tabs = array();

		if ($l) {
			$this->add_section('', $l);
		}
	}

	function add_section($title, $lyrics) {
		$lines = explode("\n", $lyrics);

		$merge = '';
        $all = array();
		foreach ($lines as $l) {
			$line = array();
			$sections = explode('[', $l);
			foreach ($sections as $s) {
				$s = $merge.$s;
				if (strpos($s,"]")!== false) {
					if(preg_match('/^(.+)\](.+)$/', $s, $regs)){
                        $line[] = array('lyrics' => $regs[2], 'tab' => trim($regs[1]));

                        $this->tabs[] = trim($regs[1]);
                        $merge = '';
                    }else{
                        $merge = $s;
                    }
				}
				else {
                    $line[] = array('lyrics' => $s, 'tab' => '');
				}
			}
			if (count($line)) {
				$all[] = $line;
			}
		}
		$this->lyrics[] = array('title' => $title,  'content' => $all);
	}

	function print_fixed() {
        $ret = '';
		foreach ($this->lyrics as $lyric) {
			if ($lyric['title']) {
				$ret .= '<b>'.$lyric['title'].'</b>';
				$ret .= "\n";
			}
			foreach ($lyric['content'] as $line) {
				foreach ($line as $sections) {
					$ret .= str_pad($sections['tab'], strlen($sections['lyrics']));
				}
				$ret .= "\n";
				foreach ($line as $sections) {
					$ret .= $sections['lyrics'];
				}
				$ret .= "\n";
			}
		}

		echo '<pre>'.$ret.'</pre>';
	}

	function print_fixed2() {
        $ret = '';
		foreach ($this->lyrics as $title => $lyric) {
			if ($lyric['title']) {
				$ret .= '<b>'.$lyric['title'].'</b><br/>';
			}
			foreach ($lyric['content'] as $line) {

				foreach ($line as $sections) {
//                    if($sections['tab'])
					    $ret .= str_pad('<span><span>'.$sections['tab'].'</span></span>',(mb_strlen($sections['lyrics'].'<span></span><span></span>','utf-8')));
				}
                $ret .= "\n";
				foreach ($line as $sections) {
					$ret .= $sections['lyrics'];
				}
                $ret .= "\n";
			}
		}

		echo '<pre id="cont" data-key="'.$this->key.'">'.$ret.'</pre>';
	}
	function print_html() {
        $ret = '';
		foreach ($this->lyrics as $title => $lyric) {
			if ($lyric['title']) {
				$ret .= '<b>'.$lyric['title'].'</b><br/>';
			}
			foreach ($lyric['content'] as $line) {
				$ret .= '<table><tr>';
				foreach ($line as $sections) {
					$ret .= '<td style="font-size: 9pt;">'.$sections['tab'].'</td>';
				}
				$ret .= '</tr><tr>';
				foreach ($line as $sections) {
					$ret .= '<td>'.$sections['lyrics'].'</td>';
				}
				$ret .= '</tr></table>';
			}
		}

		echo ''.$ret.'';
	}

	function print_tabs() {
		echo implode(' ', array_unique($this->tabs));
	}

    function objTabs()
    {
        $out = '';
        $arrTabs = array();
        if($this->tabs){
            foreach($this->tabs as $tb){
                $arrTabs[$tb]= CHtml::link($tb,'#',array('class'=>'tab'));
            }
            if(isset($arrTabs['none'])) unset($arrTabs['none']);
            $out .= implode(" <span style='color: #a7a7a7'>|</span> ",$arrTabs);
        }
        return $out;
    }

    function getStringTabs(){

    }
}