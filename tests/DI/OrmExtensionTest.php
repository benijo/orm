<?php declare(strict_types = 1);

namespace Tests\Nettrine\ORM\DI;

use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Nettrine\DBAL\DI\DbalExtension;
use Nettrine\ORM\DI\OrmAnnotationsExtension;
use Nettrine\ORM\DI\OrmExtension;
use Nettrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Tests\Nettrine\ORM\DummyEntityManager;

final class OrmExtensionTest extends TestCase
{

	/**
	 * @return void
	 */
	public function testRegisterAnnotations(): void
	{
		$loader = new ContainerLoader(__DIR__ . '/../tmp', TRUE);
		$class = $loader->load(function (Compiler $compiler): void {
			self::setUpCompiler($compiler);
		}, 'a');

		/** @var Container $container */
		$container = new $class();
		self::assertInstanceOf(EntityManager::class, $container->getByType(EntityManager::class));
	}

	/**
	 * @depends testRegisterAnnotations
	 * @return void
	 */
	public function testOwnEntityManager(): void
	{
		$loader = new ContainerLoader(__DIR__ . '/../tmp', TRUE);
		$class = $loader->load(function (Compiler $compiler): void {
			self::setUpCompiler($compiler);
			$compiler->addConfig([
				'orm' => [
					'entityManagerClass' => DummyEntityManager::class,
				],
			]);
		}, 'b');

		/** @var Container $container */
		$container = new $class();
		self::assertInstanceOf(DummyEntityManager::class, $container->getByType(DummyEntityManager::class));
	}

	/**
	 * @param Compiler $compiler
	 * @return void
	 */
	private static function setUpCompiler(Compiler $compiler): void
	{
		$compiler->addExtension('dbal', new DbalExtension());
		$compiler->addExtension('orm', new OrmExtension());
		$compiler->addExtension('orm.annotations', new OrmAnnotationsExtension());
		$compiler->addConfig([
			'parameters' => [
				'tempDir' => __DIR__ . '/../tmp',
				'appDir' => __DIR__,
			],
		]);
	}

}
