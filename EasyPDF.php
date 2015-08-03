<?php

namespace pentajeu\extensions;

use pentajeu\utils\Color;

class EasyPDF extends \TCPDF
{
	protected $font_family;
	protected $default_font = array();
	protected $save_point = array();

	public function __construct($o, $u, $f, $uni, $enc)
	{
		parent::__construct($o, $u, $f, $uni, $enc);
		$this->init();
	}

	protected function init()
	{
		$this->font_family = array(
			'font' => 'helvetica',
			'style' => '',
			'size' => 12,
			'color' => array(0, 0, 0),
			);
		$this->default_font = array(
			'font'=>'helvetica',
			'size'=>12,
			'style'=>'',
			'color'=>Color::HTMLToArray('#000')
			);

		$this->SetCreator(PDF_CREATOR);
		$this->SetAuthor("Easy PDF");
		$this->SetTitle("Easy PDF");
		$this->SetSubject("pdf");
		$this->SetKeywords("pdf");

		$this->SetPrintHeader(false);
		$this->SetPrintFooter(false);

		$this->setFontFamily();
	}

	protected function savePoint()
	{
		$this->save_point = array('x' => $this->GetX(), 'y' => $this->GetY());
	}

	/**
	 * set the font value and the color of the text
	 * @param array $param parameters here include 'font', 'style', 'size' and 'color' ('color' has to be array of red, green and blue)
	 */
	public function setFontFamily(array $param = array())
	{
		$this->font_family = array_merge($this->font_family, $param);
		$this->SetFont($this->font_family['font'], $this->font_family['style'], $this->font_family['size']);
		$this->SetTextColorArray($this->font_family['color']);
	}

	/**
	 * Create a Title and value cell
	 * @param string $key title text
	 * @param string $value value text
	 * @param array $font_style stores an array of font styles. 'key' is for key font style and 'value' is for value font style
	 */
	public function EasyKeyValueCell($key, $value, array $font_style = array())
	{
		$title_style = array('w'=>3.5, 'ln'=>0, 'align'=>'R', 'border'=>0);
		$value_style = array('w'=>6, 'align'=>'L', 'border'=>0);
		$default_font = $this->default_font;
		$font_style['key'] = isset($font_style['key'])?array_merge($default_font, $font_style['key']):$default_font;
		$font_style['value'] = isset($font_style['value'])?array_merge($default_font, $font_style['value']):$default_font;

		$this->setCellPaddings(0.2, 0, 0.2, 0);
		$this->setCellMargins('', 0.1, '', 0.1);
		$this->EasyWriteText($key, array_merge($font_style['key'], $title_style));

		if (is_array($value))
			$value_style = array_merge($value_style, array('x'=>$this->GetX()));
		else
			$value = array($value);

		foreach ($value as $v)
			$this->EasyWriteText($v, array_merge($font_style['value'], $value_style));

		$this->setFontFamily($default_font);
		$this->setCellMargins('', '', '', '');
	}

	/**
	 * Write text using TCPDF write method
	 * @param string $text text to be inserted
	 * @param array $param array that holds the parameters for write and font method
	 */
	public function EasyWrite($text, array $param = array())
	{
		$default = array(
			'h' => 0,
			'link' => '',
			'fill' => false,
			'align' => '',
			'ln' => false,
			'stretch' => 0,
			'firstline' => false,
			'firstblock' => false,
			'maxh' => 0,
			'wadj' => 0,
			'margin' => '',
			);
		$default_font = $this->default_font;
		$default = array_merge($default, $this->default_font, $param);
		$this->setFontFamily($default);
		$this->Write($default['h'], $text, $default['link'], $default['fill'], $default['align'], $default['ln'], $default['stretch'], $default['firstline'], $default['firstblock'], $default['maxh'], $default['wadj'], $default['margin']);
		$this->setFontFamily($default_font);
	}

	/**
	 * Write text using multi cell with style parameter
	 * @param string $text input text
	 * @param array $param array that holds 'style' - font parameter, and 'cell' - cell parameter
	 */
	public function EasyWriteText($text, array $param = array())
	{
		$default_font = $this->default_font;
		// $param['style'] = isset($param['style'])?array_merge($default_font, $param['style']):$default_font;
		// $param['cell'] = isset($param['cell'])?$param['cell']:array();

		$this->setFontFamily($param);
		$this->EasyMultiCell($text, $param);

		$this->setFontFamily($default_font);
	}

	public function EasyMultiCell($text, array $param = array())
	{
		$default = array(
			'w' => 0,
			'h' => 0,
			'txt' => $text,
			'border' => 0,
			'align' => 'L',
			'fill' => false,
			'ln' => 1,
			'x' => '',
			'y' => '',
			'reseth' => true,
			'stretch' => 0,
			'ishtml' => false,
			'autopadding' => true,
			'maxh' => 0,
			'valign' => 'T',
			'fitcell' => false,
			);
		$default = array_merge($default, $param);
		return parent::MultiCell($default['w'], $default['h'], $default['txt'], $default['border'], $default['align'], $default['fill'], $default['ln'], $default['x'], $default['y'], $default['reseth'], $default['stretch'], $default['ishtml'], $default['autopadding'], $default['maxh'], $default['valign'], $default['fitcell']);
	}

	public function EasyImage($file, array $param = array())
	{
		$default = array(
			'x' => '',
			'y' => '',
			'w' => 0,
			'h' => 0,
			'type' => '',
			'link' => '',
			'align' => '',
			'resize' => false,
			'dpi' => 300,
			'palign' => '',
			'ismask' => false,
			'imgmask' => false,
			'border' => 0,
			'fitbox' => false,
			'hidden' => false,
			'fitonpage' => false,
			'alt' => false,
			'altimgs' => array(),
			);
		$default = array_merge($default, $param);
		$this->Image($file, $default['x'], $default['y'], $default['w'], $default['h'], $default['type'], $default['link'], $default['align'], $default['resize'], $default['dpi'], $default['palign'], $default['ismask'], $default['imgmask'], $default['border'], $default['fitbox'], $default['hidden'], $default['fitonpage'], $default['alt'], $default['altimgs']);
	}
}