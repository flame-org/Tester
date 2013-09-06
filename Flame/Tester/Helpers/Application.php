<?php
/**
 * Class Application
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @author Filip Procházka <filip@prochazka.su>
 * @date: 23.05.13
 */
namespace Flame\Tester\Helpers;

use Flame\Tester\Tools\UIFormTestingPresenter;
use Nette\ComponentModel\IComponent;
use Nette\InvalidStateException;
use Nette\Object;
use Nette\Application\UI\Form;
use Nette\DI\Container;
use Nette\Utils\Strings;

class Application extends Object
{

	/**
	 * @throws \Nette\InvalidStateException
	 */
	public function __construct()
	{
		throw new InvalidStateException('Static class. Calling constructor is banned.');
	}

	/**
	 * @param Container $context
	 * @param Form      $form
	 * @param array     $values
	 * @return mixed
	 */
	public static function submitForm(Container $context, Form $form, array $values = array())
	{
		$get = $form->getMethod() !== Form::POST ? $values : array();
		$post = $form->getMethod() === Form::POST ? $values : array();
		list($post, $files) = static::separateFilesFromPost($post);

		$presenter = new UIFormTestingPresenter($form);
		$context->callMethod(array($presenter, 'injectPrimary'));

		return $presenter->run(new \Nette\Application\Request(
			'presenter',
			strtoupper($form->getMethod()),
			array('do' => 'form-submit', 'action' => 'default') + $get,
			$post,
			$files
		));
	}

	/**
	 * @param array $post
	 * @param array $files
	 *
	 * @return array
	 */
	public static function separateFilesFromPost(array $post, array $files = array())
	{
		foreach ($post as $key => $value) {
			if (is_array($value)) {
				list($pPost, $pFiles) = static::separateFilesFromPost($value);
				unset($post[$key]);

				if ($pPost) {
					$post[$key] = $pPost;
				}
				if ($pFiles) {
					$files[$key] = $pFiles;
				}
			}

			if ($value instanceof \Nette\Http\FileUpload) {
				$files[$key] = $value;
				unset($post[$key]);
			}
		}

		return array($post, $files);
	}

	/**
	 * @param Container  $context
	 * @param IComponent $component
	 * @param            $presenter
	 * @param string     $name
	 * @return mixed
	 */
	public static function attachToPresenter(Container $context, IComponent $component, $presenter, $name = 'component')
	{
		$context->callMethod(array($presenter, 'injectPrimary'));
		$component->setParent($presenter, $name);
		return $presenter;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 * @return string
	 * @throws \Exception
	 */
	public static function renderForm(Form $form)
	{
		ob_start();
		try {
			$form->render();
		} catch (\Exception $e) {
			ob_end_clean();
			throw $e;
		}

		return Strings::normalize(ob_get_clean());
	}

}