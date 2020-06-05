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

	public function isImage(string $imagePath): bool {
		if (!file_exists($imagePath)) {
			return false;
		}

		$prefix = $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
		return in_array($extension, $this->allowedExtensions);
	}

	public function convert(string $imagePath, array $params): ?string {
		if (!file_exists($imagePath)) {
			return null;
		}

		$prefix = $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
		if (!in_array($extension, $this->allowedExtensions)) {
			throw new \Exception('Invalid image extension: ' . $extension, 1590408262);
		}

		$fileName = pathinfo($imagePath, PATHINFO_FILENAME);

		$parameters = array_merge([
			'-quality', 85,
			'-interlace', 'Plane',
		], $params);

		if (isset($parameters['fileExtension'])) {
			$extension = $parameters['fileExtension'];
			if ($extension === 'jpg' && !in_array('-background', $parameters)) {
				$parameters[] = '-background';
				$parameters[] = 'white';
				$parameters[] = '-flatten';
			}
			unset($parameters['fileExtension']);
		}

		if (isset($parameters['fileName'])) {
			$fileName = pathinfo($parameters['fileName'], PATHINFO_FILENAME);
			unset($parameters['fileName']);
		}
		// $fileName = $this->transliterator->transliterate($fileName);

		$hash = md5($imagePath . serialize($parameters));
		$subdir = $hash[0] . '/' . $hash[1];

		$outputDirectory = '_processed_/' . $subdir . '/';
		$outputFile = $outputDirectory . $fileName . '_' . $hash . '.' . $extension;
		if (file_exists($outputFile)) {
			return '/' . $outputFile;
		}

		if (!is_dir($outputDirectory)) {
			mkdir($outputDirectory, 0755, true);
		}

		$process = new Process(array_merge([
			'convert',
			$prefix . ':' . $imagePath,
		], $parameters, [$outputFile]));
		$process->run();

		if ($process->isSuccessful()) {
			return '/' . $outputFile;
		}

		if (!empty($_SERVER['VIERWD_CONFIG'])) {
			debug4wd($process);
		}
		throw new \Exception('Could not process file', 1590410057);
	}
}
