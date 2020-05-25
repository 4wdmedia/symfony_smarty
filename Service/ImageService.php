<?php
declare(strict_types = 1);

namespace Vierwd\Symfony\Smarty\Service;

use Symfony\Component\Process\Process;

class ImageService {

	private $command;

	protected $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'tif', 'tiff', 'bmp', 'pcx', 'tga', 'ico'];

	public function __construct(string $command = null) {
		if (!$command || !is_executable($command)) {
			$paths = explode(PATH_SEPARATOR, $_SERVER['PATH']);
			$paths[] = '/usr/bin';
			$paths[] = '/usr/local/bin';
			$paths = array_unique($paths);
			foreach ($paths as $path) {
				if (is_executable($path . DIRECTORY_SEPARATOR . 'convert')) {
					$command = $path . DIRECTORY_SEPARATOR . 'convert';
				}
			}
		}

		if (!is_executable($command)) {
			throw new Exception('Could not find ImageMagick executable', 1590406170);
		}

		$this->command = $command;
	}

	public function convert(string $imagePath, array $params): ?string {
		if (!file_exists($imagePath)) {
			return null;
		}

		$extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
		if (!in_array($extension, $this->allowedExtensions)) {
			throw new \Exception('Invalid image extension: ' . $extension, 1590408262);
		}

		$filename = pathinfo($imagePath, PATHINFO_FILENAME);
		$outputFile = '_processed_/' . $filename . '_' . md5(serialize($params)) . $extension;

		$parameters = [
			'-resize', '150x150^',
			'-quality', 85,
			'-interlace', 'Plane',
		];

		$process = new Process(array_merge([
			'convert',
			$extension . ':' . $imagePath,
		], $parameters, [$outputFile]));
		$process->run();

		if ($process->isSuccessful()) {
			return $outputFile;
		}

		return null;
	}
}
