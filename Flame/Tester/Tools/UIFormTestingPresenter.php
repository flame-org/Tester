<?php
/**
 * UIFormTestingPresenter.php
 *
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 * @package Flame
 *
 * @date    18.11.12
 */

namespace Flame\Tester\Tools;

use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

class UIFormTestingPresenter extends Presenter
{

	/** @var \Nette\Application\UI\Form */
	private $form;

	/**
	 * @param \Nette\Application\UI\Form $form
	 */
	public function __construct(Form $form)
	{
		parent::__construct();
		$this->form = $form;
	}

	/**
	 * Just terminate the rendering
	 */
	public function renderDefault()
	{
		$this->terminate();
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm()
	{
		return $this->form;
	}

}
